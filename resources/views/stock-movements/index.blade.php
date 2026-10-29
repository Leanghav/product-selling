
@extends('layouts.app')

@section('title', 'Stock Movements')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <h2>Stock Movements</h2>
        <p class="text-muted mb-0">
            History of stock coming in and going out
        </p>
    </div>

    <form method="GET" action="{{ route('stock-movements.index') }}" class="mb-3">
        <div class="input-group">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search product, SKU or reference..."
                value="{{ request('search') }}"
            >

            <button class="btn btn-primary" type="submit">Search</button>

            <a href="{{ route('stock-movements.index') }}"
               class="btn btn-outline-secondary">
                Reset
            </a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Stock Before</th>
                        <th class="text-end">Stock After</th>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Note</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td>{{ $movements->firstItem() + $loop->index }}</td>

                            <td>
                                {{ $movement->created_at?->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $movement->product?->name ?? 'Product not found' }}
                                @if ($movement->product?->sku)
                                    <div class="text-muted small">
                                        SKU: {{ $movement->product->sku }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                @php
                                    $type = strtolower($movement->type ?? '');
                                @endphp

                                @if (in_array($type, ['in', 'purchase', 'return_in']))
                                    <span class="badge bg-success">
                                        {{ ucfirst($type) }}
                                    </span>
                                @elseif (in_array($type, ['out', 'sale', 'return_out']))
                                    <span class="badge bg-danger">
                                        {{ ucfirst($type) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($type ?: 'Unknown') }}
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">
                                {{ number_format((float) $movement->quantity, 0) }}
                            </td>

                            <td class="text-end">
                                {{ number_format((float) $movement->stock_before, 0) }}
                            </td>

                            <td class="text-end">
                                {{ number_format((float) $movement->stock_after, 0) }}
                            </td>

                            <td>{{ $movement->reference ?? '—' }}</td>

                            <td>{{ $movement->user?->name ?? 'System' }}</td>

                            <td>{{ $movement->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                No stock movements found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body">
            {{ $movements->links() }}
        </div>
    </div>

</div>
@endsection
