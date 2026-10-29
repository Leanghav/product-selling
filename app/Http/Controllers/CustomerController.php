<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $customers = Customer::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact(
            'customers',
            'search'
        ));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'phone' => 'nullable|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'balance' => $request->balance ?? 0,
            'status' => $request->has('status'),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully!');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'sales' => function ($query) {
                $query->latest();
            }
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|max:255',
            'phone' => 'nullable|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'balance' => $request->balance ?? 0,
            'status' => $request->has('status'),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully!');
    }
}