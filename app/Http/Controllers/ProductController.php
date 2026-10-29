<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search
        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        // Stock Filter
        if ($request->stock === 'in') {
            $query->where('quantity', '>', 0);
        }

        if ($request->stock === 'out') {
            $query->where('quantity', '<=', 0);
        }

        // Pagination
        $products = $query
            ->latest()
            ->paginate(10);

        // Categories
        $categories = Category::orderBy('name')->get();

        return view(
            'products.index',
            compact('products', 'categories')
        );
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view(
            'products.create',
            compact('categories')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = $request
                ->file('image')
                ->store('products', 'public');
        }

        Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image' => $imageName,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product created successfully!'
            );
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view(
            'products.edit',
            compact('product', 'categories')
        );
    }

    public function update(
        Request $request,
        Product $product
    ) {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = $product->image;

        if ($request->hasFile('image')) {

            if ($product->image) {
                Storage::disk('public')
                    ->delete($product->image);
            }

            $imageName = $request
                ->file('image')
                ->store('products', 'public');
        }

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image' => $imageName,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product updated successfully!'
            );
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')
                ->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product deleted successfully!'
            );
    }
}