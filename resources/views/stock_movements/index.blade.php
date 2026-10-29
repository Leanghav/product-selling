
@extends('layouts.app')

@section('title', 'Stock Movements')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                Stock Movement
            </h3>

            <p class="text-muted mb-0">
                Stock in / out history
            </p>
        </div>

        <a href="{{ route('products.index') }}"
           class="btn btn-secondary">

            ← Products

        </a>

    </div>


    {{-- Filters --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('stock-movements.index') }}">

                <div class="row g-3">

                    {{-- Search --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Search Product
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Product name..."
                        >

                    </div>


                    {{-- Type --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            Type
                        </label>

                        <select
                            name="type"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                value="sale"
                                {{ request('type') == 'sale' ? 'selected' : '' }}
                            >
                                Sale
                            </option>

                            <option
                                value="return"
                                {{ request('type') == 'return' ? 'selected' : '' }}
                            >
                                Return
                            </option>

                            <option
                                value="in"
                                {{ request('type') == 'in' ? 'selected' : '' }}
                            >
                                Stock In
                            </option>

                        </select>

                    </div>


                    {{-- Product --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Product
                        </label>

                        <select
                            name="product_id"
                            class="form-select"
                        >

                            <option value="">
                                All Products
                            </option>

                            @foreach($products as $product)

                                <option
                                    value="{{ $product->id }}"
                                    {{ request('product_id') == $product->id ? 'selected' : '' }}
                                >
                                    {{ $product->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- From --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            From
                        </label>

                        <input
                            type="date"
                            name="from_date"
                            class="form-control"
                            value="{{ request('from_date') }}"
                        >

                    </div>


                    {{-- To --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            To
                        </label>

                        <input
                            type="date"
                            name="to_date"
                            class="form-control"
                            value="{{ request('to_date') }}"
                        >

                    </div>


                    {{-- Buttons --}}
                    <div class="col-12">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Search
                        </button>

                        <a
                            href="{{ route('stock-movements.index') }}"
                            class="btn btn-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Table --}}
    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>Date</th>

                            <th>Product</th>

                            <th>Type</th>

                            <th>Quantity</th>

                            <th>Stock Before</th>

                            <th>Stock After</th>

                            <th>Reference</th>

                            <th>User</th>

                            <th>Note</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($movements as $movement)

                            <tr>

                                <td>
                                    {{ $movements->firstItem() + $loop->index }}
                                </td>


                                <td>

                                    {{ $movement->created_at?->format('Y-m-d H:i') }}

                                </td>


                                <td>

                                    <strong>
                                        {{ $movement->product?->name ?? 'Deleted Product' }}
                                    </strong>

                                </td>


                                <td>

                                    @if($movement->type === 'sale')

                                        <span class="badge bg-danger">
                                            SALE
                                        </span>

                                    @elseif($movement->type === 'return')

                                        <span class="badge bg-warning text-dark">
                                            RETURN
                                        </span>

                                    @elseif($movement->type === 'in')

                                        <span class="badge bg-success">
                                            STOCK IN
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            {{ strtoupper($movement->type) }}
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($movement->quantity < 0)

                                        <span class="text-danger fw-bold">
                                            {{ $movement->quantity }}
                                        </span>

                                    @else

                                        <span class="text-success fw-bold">
                                            +{{ $movement->quantity }}
                                        </span>

                                    @endif

                                </td>


                                <td>
                                    {{ $movement->stock_before }}
                                </td>


                                <td>

                                    <strong>
                                        {{ $movement->stock_after }}
                                    </strong>

                                </td>


                                <td>

                                    {{ $movement->reference ?? '-' }}

                                </td>


                                <td>

                                    {{ $movement->user?->name ?? 'System' }}

                                </td>


                                <td>

                                    {{ $movement->note ?? '-' }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="10"
                                    class="text-center py-5 text-muted"
                                >

                                    No stock movement found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($movements->hasPages())

            <div class="card-footer">

                {{ $movements->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
