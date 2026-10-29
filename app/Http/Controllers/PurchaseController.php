<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier')
            ->withCount('items')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('purchase_no', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                        $supplierQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('company', 'like', "%{$search}%");
                    });
            });
        }

        $purchases = $query->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 1)
            ->orderBy('name')
            ->get();

        $products = Product::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'paid' => [
                'required',
                'numeric',
                'min:0',
            ],
            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'items.*.cost_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $discount = (float) ($validated['discount'] ?? 0);
        $paid = (float) $validated['paid'];

        $purchase = DB::transaction(function () use (
            $validated,
            $discount,
            $paid
        ) {
            $supplier = Supplier::whereKey($validated['supplier_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $subtotal = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$product->status) {
                    throw ValidationException::withMessages([
                        'items' => "Product {$product->name} is inactive.",
                    ]);
                }

                $quantity = (int) $item['quantity'];
                $costPrice = (float) $item['cost_price'];
                $lineSubtotal = round($quantity * $costPrice, 2);

                $subtotal += $lineSubtotal;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'cost_price' => $costPrice,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $subtotal = round($subtotal, 2);

            if ($discount > $subtotal) {
                throw ValidationException::withMessages([
                    'discount' => 'Discount cannot exceed the subtotal.',
                ]);
            }

            $total = round($subtotal - $discount, 2);

            if ($paid > $total) {
                throw ValidationException::withMessages([
                    'paid' => 'Paid amount cannot exceed the purchase total.',
                ]);
            }

            $due = round($total - $paid, 2);

            if ($due <= 0) {
                $paymentStatus = 'paid';
            } elseif ($paid > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'unpaid';
            }

            $purchaseNo = 'PUR-' . now()->format('YmdHis')
                . '-' . strtoupper(substr(uniqid(), -4));

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'purchase_no' => $purchaseNo,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'paid' => $paid,
                'due' => $due,
                'payment_status' => $paymentStatus,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($preparedItems as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];
                $stockBefore = (int) $product->quantity;
                $stockAfter = $stockBefore + $quantity;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'cost_price' => $item['cost_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product->quantity = $stockAfter;
                $product->cost_price = $item['cost_price'];
                $product->save();

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => $purchaseNo,
                    'note' => 'Purchase ' . $purchaseNo,
                    'user_id' => Auth::id(),
                ]);
            }

            // Update supplier outstanding balance.
            $supplier->balance = (float) $supplier->balance + $due;
            $supplier->save();

            return $purchase;
        });

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Purchase created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('items.product');

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('purchases.edit', compact(
            'purchase',
            'suppliers',
            'products'
        ));
    }

    public function update(Request $request, Purchase $purchase)
    {
        return redirect()
            ->route('purchases.show', $purchase)
            ->with(
                'error',
                'Editing a saved purchase is disabled for now to protect stock and supplier balances.'
            );
    }

    public function destroy(Purchase $purchase)
    {
        return redirect()
            ->route('purchases.index')
            ->with(
                'error',
                'Deleting a saved purchase is disabled for now to protect stock history and supplier balances.'
            );
    }
}