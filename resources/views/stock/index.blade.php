@extends('layouts.app')

@section('title', 'Stock')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h2>Stock Movements</h2>
</div>

<div class="table-responsive">

    <table class="table table-bordered table-hover">

        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Before</th>
                <th>After</th>
                <th>Reference</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>

            @forelse($movements as $movement)

                <tr>

                    <td>{{ $movement->id }}</td>

                    <td>
                        {{ $movement->product->name ?? 'N/A' }}
                    </td>

                    <td>
                        <span class="badge bg-info">
                            {{ $movement->type }}
                        </span>
                    </td>

                    <td>
                        {{ $movement->quantity }}
                    </td>

                    <td>
                        {{ $movement->stock_before }}
                    </td>

                    <td>
                        {{ $movement->stock_after }}
                    </td>

                    <td>
                        {{ $movement->reference ?? '-' }}
                    </td>

                    <td>
                        {{ $movement->created_at->format('Y-m-d H:i') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="8" class="text-center">
                        No Stock Movements Found
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection