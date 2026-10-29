<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $suppliers = Supplier::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'company' => 'nullable|max:255',
            'phone' => 'nullable|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['status'] = $request->has('status');

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load([
            'purchases' => function ($query) {
                $query->latest();
            }
        ]);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'company' => 'nullable|max:255',
            'phone' => 'nullable|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['status'] = $request->has('status');

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists()) {
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'Cannot delete supplier with purchase history.');
        }

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }
}