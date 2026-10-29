
@extends('layouts.app')

@section('title', 'Purchase Details')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Purchase Details</h2>
            <p class="text-muted mb-0">
                Purchase No: {{ $purchase->purchase_no }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                Print
            </button>

            <a href="{{ route('purchases.index') }}" class="btn btn-primary">
                Back to Purchases
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Purchase Information</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Purchase No.</strong>
                    <div>{{ $purchase->purchase_no }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Supplier</strong>
                    <div>{{ $purchase->supplier?->name ?? 'N/A' }}</div>
                    @if ($purchase->supplier?->company)
                        <small class="text-muted">{{ $purchase->supplier->company }}</small>
                    @endif
                </div>

                <div class="col-md-4">
                    <strong>Date</strong>
                    <div>{{ $purchase->created_at?->format('d/m/Y H:i') }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Payment Status</strong>
                    @php
                        $status = strtolower($purchase->payment_status ?? 'unpaid');
                        $badge = match ($status) {
                            'paid' => 'success',
                            'partial' => 'warning',
                            default => 'danger',
                        };
                    @endphp
                    <div>
                        <span class="badge bg-{{ $badge }}">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                </div>

                <div class="col-md-8">
                    <strong>Note</strong>
                    <div>{{ $purchase->note ?: '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Purchased Products</div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($purchase->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product?->name ?? 'Product not found' }}</td>
                            <td class="text-end">{{ number_format($item->quantity) }}</td>
                            <td class="text-end">{{ number_format((float) $item->cost_price, 2) }}</td>
                            <td class="text-end">
                                {{ number_format((float) $item->subtotal, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No purchase items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>{{ number_format((float) $purchase->subtotal, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount</span>
                        <strong>{{ number_format((float) $purchase->discount, 2) }}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Total</span>
                        <strong>{{ number_format((float) $purchase->total, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Paid</span>
                        <strong class="text-success">
                            {{ number_format((float) $purchase->paid, 2) }}
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Due</span>
                        <strong class="text-danger">
                            {{ number_format((float) $purchase->due, 2) }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
@media print {
    .btn, nav, aside, .sidebar {
        display: none !important;
    }

    .container-fluid {
        width: 100% !important;
    }
}
</style>
@endsection
