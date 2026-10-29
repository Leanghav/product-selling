@extends('layouts.app')

@section('title', 'Products')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Products</h2>
        <p class="text-muted mb-0">Manage your products and inventory</p>
    </div>

    <a href="{{ route('products.create') }}" class="btn btn-primary">
        + Add Product
    </a>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

{{-- Search & Filter --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">

        <form method="GET"
              action="{{ route('products.index') }}">

            <div class="row g-3">

                {{-- Search --}}
                <div class="col-md-4">

                    <label class="form-label">
                        Search Product
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by name..."
                        value="{{ request('search') }}"
                    >

                </div>

                {{-- Category --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Category
                    </label>

                    <select name="category_id"
                            class="form-select">

                        <option value="">
                            All Categories
                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Stock --}}
                <div class="col-md-3">

                    <label class="form-label">
                        Stock
                    </label>

                    <select name="stock"
                            class="form-select">

                        <option value="">
                            All Stock
                        </option>

                        <option value="in"
                            {{ request('stock') == 'in' ? 'selected' : '' }}>
                            In Stock
                        </option>

                        <option value="out"
                            {{ request('stock') == 'out' ? 'selected' : '' }}>
                            Out of Stock
                        </option>

                    </select>

                </div>

                {{-- Buttons --}}
                <div class="col-md-2 d-flex align-items-end gap-2">

                    <button type="submit"
                            class="btn btn-primary">
                        Search
                    </button>

                    <a href="{{ route('products.index') }}"
                       class="btn btn-secondary">
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>
</div>


{{-- Products Table --}}
<div class="card shadow-sm">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th width="180">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($products as $product)

                        <tr>

                            {{-- ID --}}
                            <td>
                                {{ $product->id }}
                            </td>


                            {{-- Image --}}
                            <td>

                                @if($product->image)

                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        width="60"
                                        height="60"
                                        style="
                                            object-fit: cover;
                                            border-radius: 8px;
                                        "
                                        alt="{{ $product->name }}"
                                    >

                                @else

                                    <span class="text-muted">
                                        No Image
                                    </span>

                                @endif

                            </td>


                            {{-- Name --}}
                            <td>

                                <strong>
                                    {{ $product->name }}
                                </strong>

                            </td>


                            {{-- Category --}}
                            <td>

                                {{ $product->category?->name ?? 'No Category' }}

                            </td>


                            {{-- Price --}}
                            <td>

                                <strong>
                                    ${{ number_format($product->price, 2) }}
                                </strong>

                            </td>


                            {{-- Stock --}}
                            <td>

                                @if($product->quantity <= 0)

                                    <span class="badge bg-danger">
                                        Out of Stock
                                    </span>

                                @elseif($product->quantity <= 5)

                                    <span class="badge bg-warning text-dark">
                                        Low Stock: {{ $product->quantity }}
                                    </span>

                                @else

                                    <span class="badge bg-success">
                                        {{ $product->quantity }}
                                    </span>

                                @endif

                            </td>


                            {{-- Status --}}
                            <td>

                                @if($product->status)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            {{-- Action --}}
                            <td>

                                <a
                                    href="{{ route('products.edit', $product->id) }}"
                                    class="btn btn-warning btn-sm"
                                >
                                    Edit
                                </a>

                                <form
                                    action="{{ route('products.destroy', $product->id) }}"
                                    method="POST"
                                    class="d-inline"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this product?')"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center py-5">

                                <h5 class="text-muted">
                                    No Products Found
                                </h5>

                                <p class="text-muted">
                                    Try changing your search or filter.
                                </p>

                                <a
                                    href="{{ route('products.create') }}"
                                    class="btn btn-primary"
                                >
                                    + Add Product
                                </a>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}
    @if($products->hasPages())

        <div class="card-footer">

            {{ $products->withQueryString()->links() }}

        </div>

    @endif

</div>

@endsection