<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer')
            ->latest()
            ->get();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        $products = Product::where('status', 1)
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact(
            'customers',
            'products'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',

            'invoice_no' => 'nullable|string|max:255',

            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',

            'payment_method' => 'required|in:cash,card,bank,other',

            'note' => 'nullable|string',

            'items' => 'required|array|min:1',

            'items.*.product_id' => 'required|exists:products,id',

            'items.*.quantity' => 'required|integer|min:1',

            'items.*.price' => 'required|numeric|min:0',
        ]);


        $sale = DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | Generate Invoice Number
            |--------------------------------------------------------------------------
            */

            do {
                $invoiceNo =
                    'INV-' .
                    now()->format('YmdHis') .
                    '-' .
                    random_int(100, 999);

            } while (
                Sale::where('invoice_no', $invoiceNo)->exists()
            );


            /*
            |--------------------------------------------------------------------------
            | Calculate Subtotal
            |--------------------------------------------------------------------------
            */

            $subtotal = 0;

            foreach ($request->items as $item) {

                $quantity = (int) $item['quantity'];

                $price = (float) $item['price'];

                $subtotal += $quantity * $price;
            }


            /*
            |--------------------------------------------------------------------------
            | Discount
            |--------------------------------------------------------------------------
            */

            $discount = (float) ($request->discount ?? 0);

            if ($discount > $subtotal) {
                $discount = $subtotal;
            }


            /*
            |--------------------------------------------------------------------------
            | Tax
            |--------------------------------------------------------------------------
            */

            $tax = (float) ($request->tax ?? 0);


            /*
            |--------------------------------------------------------------------------
            | Total
            |--------------------------------------------------------------------------
            */

            $total = ($subtotal - $discount) + $tax;


            /*
            |--------------------------------------------------------------------------
            | Paid / Change
            |--------------------------------------------------------------------------
            */

            $paid = (float) ($request->paid ?? 0);

            $change = max(0, $paid - $total);

            $paidAmount = min($paid, $total);


            /*
            |--------------------------------------------------------------------------
            | Create Sale
            |--------------------------------------------------------------------------
            */

            $sale = Sale::create([
                'invoice_no' => $invoiceNo,

                'customer_id' => $request->customer_id,

                'subtotal' => $subtotal,

                'discount' => $discount,

                'tax' => $tax,

                'total' => $total,

                'paid' => $paidAmount,

                'change' => $change,

                'payment_method' => $request->payment_method,

                'user_id' => auth()->id(),

                'note' => $request->note,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Sale Items + Stock OUT
            |--------------------------------------------------------------------------
            */

            foreach ($request->items as $item) {

                $quantity = (int) $item['quantity'];

                $price = (float) $item['price'];


                /*
                | Lock Product
                */

                $product = Product::lockForUpdate()
                    ->findOrFail($item['product_id']);


                /*
                | Check Stock
                */

                if ($product->quantity < $quantity) {

                    throw new \Exception(
                        'Not enough stock for product: ' .
                        $product->name
                    );
                }


                /*
                | Stock Before
                */

                $oldStock = (int) $product->quantity;


                /*
                | Stock After
                */

                $newStock = $oldStock - $quantity;


                /*
                | Item Total
                */

                $itemTotal = $quantity * $price;


                /*
                | Create Sale Item
                */

                SaleItem::create([
                    'sale_id' => $sale->id,

                    'product_id' => $product->id,

                    'quantity' => $quantity,

                    'price' => $price,

                    'cost_price' => $product->cost_price ?? 0,

                    'discount' => 0,

                    'total' => $itemTotal,
                ]);


                /*
                | Update Product Stock
                */

                $product->quantity = $newStock;

                $product->save();


                /*
                | Create Stock Movement
                */

                StockMovement::create([
                    'product_id' => $product->id,

                    'type' => 'sale',

                    'quantity' => -$quantity,

                    'stock_before' => $oldStock,

                    'stock_after' => $newStock,

                    'reference' => 'Sale #' . $sale->invoice_no,

                    'user_id' => auth()->id(),

                    'note' => 'Stock Out from sale',
                ]);
            }


            return $sale;
        });


        /*
        |--------------------------------------------------------------------------
        | Redirect to Invoice
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('sales.show', $sale)
            ->with(
                'success',
                'Sale created successfully!'
            );
    }


    public function show(Sale $sale)
    {
        $sale->load([
            'customer',
            'items.product'
        ]);

        return view(
            'sales.show',
            compact('sale')
        );
    }

    public function edit(Sale $sale)
    {
        $customers = Customer::orderBy('name')->get();

        $products = Product::orderBy('name')->get();

        $sale->load('items.product');

        return view(
            'sales.edit',
            compact(
                'sale',
                'customers',
                'products'
            )
        );
    }


    public function update(
        Request $request,
        Sale $sale
    ) {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',

            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',

            'payment_method' => 'required|in:cash,card,bank,other',

            'note' => 'nullable|string',

            'items' => 'required|array|min:1',

            'items.*.product_id' => 'required|exists:products,id',

            'items.*.quantity' => 'required|integer|min:1',

            'items.*.price' => 'required|numeric|min:0',
        ]);


        DB::transaction(function () use ($request, $sale) {

            /*
            |--------------------------------------------------------------------------
            | Load Old Items
            |--------------------------------------------------------------------------
            */

            $sale->load('items');


            /*
            |--------------------------------------------------------------------------
            | Save Old Quantities
            |--------------------------------------------------------------------------
            */

            $oldItems = [];

            foreach ($sale->items as $oldItem) {

                $oldItems[$oldItem->product_id] =
                    ($oldItems[$oldItem->product_id] ?? 0)
                    + (int) $oldItem->quantity;
            }


            /*
            |--------------------------------------------------------------------------
            | Save New Quantities
            |--------------------------------------------------------------------------
            */

            $newItems = [];

            foreach ($request->items as $item) {

                $productId = (int) $item['product_id'];

                $quantity = (int) $item['quantity'];

                $newItems[$productId] =
                    ($newItems[$productId] ?? 0)
                    + $quantity;
            }


            /*
            |--------------------------------------------------------------------------
            | Calculate Subtotal
            |--------------------------------------------------------------------------
            */

            $subtotal = 0;

            foreach ($request->items as $item) {

                $quantity = (int) $item['quantity'];

                $price = (float) $item['price'];

                $subtotal += $quantity * $price;
            }


            /*
            |--------------------------------------------------------------------------
            | Discount
            |--------------------------------------------------------------------------
            */

            $discount = (float) ($request->discount ?? 0);

            if ($discount > $subtotal) {
                $discount = $subtotal;
            }


            /*
            |--------------------------------------------------------------------------
            | Tax
            |--------------------------------------------------------------------------
            */

            $tax = (float) ($request->tax ?? 0);


            /*
            |--------------------------------------------------------------------------
            | Total
            |--------------------------------------------------------------------------
            */

            $total = ($subtotal - $discount) + $tax;


            /*
            |--------------------------------------------------------------------------
            | Paid / Change
            |--------------------------------------------------------------------------
            */

            $paid = (float) ($request->paid ?? 0);

            $change = max(0, $paid - $total);

            $paidAmount = min($paid, $total);


            /*
            |--------------------------------------------------------------------------
            | Check + Adjust Stock
            |--------------------------------------------------------------------------
            */

            $productIds = array_unique(
                array_merge(
                    array_keys($oldItems),
                    array_keys($newItems)
                )
            );


            foreach ($productIds as $productId) {

                $oldQuantity =
                    $oldItems[$productId] ?? 0;

                $newQuantity =
                    $newItems[$productId] ?? 0;


                /*
                |--------------------------------------------------------------------------
                | Difference
                |--------------------------------------------------------------------------
                |
                | Example:
                |
                | Old = 2
                | New = 5
                | Difference = +3
                | Need 3 more stock
                |
                | Old = 5
                | New = 2
                | Difference = -3
                | Return 3 stock
                |
                */

                $difference =
                    $newQuantity - $oldQuantity;


                /*
                |--------------------------------------------------------------------------
                | Lock Product
                |--------------------------------------------------------------------------
                */

                $product = Product::lockForUpdate()
                    ->find($productId);


                if (!$product) {
                    throw new \Exception(
                        'Product not found: ' . $productId
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Quantity Increased
                |--------------------------------------------------------------------------
                */

                if ($difference > 0) {

                    if ($product->quantity < $difference) {

                        throw new \Exception(
                            'Not enough stock for product: ' .
                            $product->name .
                            '. Available: ' .
                            $product->quantity .
                            ', Required: ' .
                            $difference
                        );
                    }


                    $stockBefore =
                        (int) $product->quantity;

                    $stockAfter =
                        $stockBefore - $difference;


                    $product->quantity =
                        $stockAfter;

                    $product->save();


                    /*
                    | Stock Movement OUT
                    */

                    StockMovement::create([
                        'product_id' =>
                            $product->id,

                        'type' =>
                            'sale',

                        'quantity' =>
                            -$difference,

                        'stock_before' =>
                            $stockBefore,

                        'stock_after' =>
                            $stockAfter,

                        'reference' =>
                            'Edit Sale #' .
                            $sale->invoice_no,

                        'user_id' =>
                            auth()->id(),

                        'note' =>
                            'Additional stock out from edited sale',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Quantity Decreased
                |--------------------------------------------------------------------------
                */

                elseif ($difference < 0) {

                    $returnQuantity =
                        abs($difference);


                    $stockBefore =
                        (int) $product->quantity;

                    $stockAfter =
                        $stockBefore + $returnQuantity;


                    $product->quantity =
                        $stockAfter;

                    $product->save();


                    /*
                    | Stock Movement IN
                    */

                    StockMovement::create([
                        'product_id' =>
                            $product->id,

                        'type' =>
                            'return',

                        'quantity' =>
                            $returnQuantity,

                        'stock_before' =>
                            $stockBefore,

                        'stock_after' =>
                            $stockAfter,

                        'reference' =>
                            'Edit Sale #' .
                            $sale->invoice_no,

                        'user_id' =>
                            auth()->id(),

                        'note' =>
                            'Stock returned from edited sale',
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Delete Old Sale Items
            |--------------------------------------------------------------------------
            */

            $sale->items()->delete();


            /*
            |--------------------------------------------------------------------------
            | Update Sale
            |--------------------------------------------------------------------------
            */

            $sale->update([
                'customer_id' =>
                    $request->customer_id,

                'subtotal' =>
                    $subtotal,

                'discount' =>
                    $discount,

                'tax' =>
                    $tax,

                'total' =>
                    $total,

                'paid' =>
                    $paidAmount,

                'change' =>
                    $change,

                'payment_method' =>
                    $request->payment_method,

                'note' =>
                    $request->note,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Create New Sale Items
            |--------------------------------------------------------------------------
            */

            foreach ($request->items as $item) {

                $product =
                    Product::findOrFail(
                        $item['product_id']
                    );


                $quantity =
                    (int) $item['quantity'];


                $price =
                    (float) $item['price'];


                $itemTotal =
                    $quantity * $price;


                SaleItem::create([
                    'sale_id' =>
                        $sale->id,

                    'product_id' =>
                        $product->id,

                    'quantity' =>
                        $quantity,

                    'price' =>
                        $price,

                    'cost_price' =>
                        $product->cost_price ?? 0,

                    'discount' =>
                        0,

                    'total' =>
                        $itemTotal,
                ]);
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('sales.show', $sale)
            ->with(
                'success',
                'Sale updated successfully!'
            );
    }






   public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {

            // Load sale items
            $sale->load('items');

            foreach ($sale->items as $item) {

                // Find product
                $product = Product::find($item->product_id);

                if ($product) {

                    // Stock before restore
                    $stockBefore = $product->quantity;

                    // Restore stock
                    $product->quantity =
                        $product->quantity + $item->quantity;

                    // Save product
                    $product->save();

                    // Stock after restore
                    $stockAfter = $product->quantity;

                    // Create stock movement
                    StockMovement::create([
                        'product_id'   => $product->id,
                        'type'         => 'in',
                        'quantity'     => $item->quantity,
                        'stock_before' => $stockBefore,
                        'stock_after'  => $stockAfter,
                        'reference'    => $sale->invoice_no,
                        'note'         => 'Stock restored from deleted invoice',
                        'user_id'      => auth()->id(),
                    ]);
                }
            }

            // Delete sale items
            $sale->items()->delete();

            // Delete invoice
            $sale->delete();
        });

        return redirect()
            ->route('invoices.index')
            ->with(
                'success',
                'Invoice deleted and stock restored successfully!'
            );
    }

    
   public function invoices(Request $request)
    {
        $query = Sale::with('customer');

        // Search Invoice No / Customer Name
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('invoice_no', 'like', '%' . $search . '%')

                ->orWhereHas('customer', function ($customerQuery) use ($search) {

                    $customerQuery->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    );

                });

            });
        }


        // Payment Method Filter
        if ($request->filled('payment_method')) {

            $query->where(
                'payment_method',
                $request->payment_method
            );
        }


        // From Date
        if ($request->filled('from_date')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );
        }


        // To Date
        if ($request->filled('to_date')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );
        }


        // Pagination
        $sales = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return view(
            'invoices.index',
            compact('sales')
        );
    }
}