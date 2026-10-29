
@extends('layouts.app')

@section('title', 'Purchases')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Purchases</h2>
            <p class="text-muted mb-0">Purchase history and stock-in records</p>
        </div>

        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Purchase
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('purchases.index') }}" class="mb-3">
        <div class="input-group">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search purchase number or supplier..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-outline-primary">
                Search
            </button>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                Reset
            </a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Purchase No.</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Due</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $purchases->firstItem() + $loop->index }}</td>
                            <td>{{ $purchase->purchase_no }}</td>
                            <td>
                                {{ $purchase->supplier?->name ?? 'N/A' }}
                            </td>
                            <td>
                                {{ $purchase->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="text-end">
                                {{ number_format((float) $purchase->total, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format((float) $purchase->paid, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format((float) $purchase->due, 2) }}
                            </td>
                            <td>
                                @php
                                    $status = strtolower($purchase->payment_status ?? 'unpaid');
                                    $badge = match ($status) {
                                        'paid' => 'success',
                                        'partial' => 'warning',
                                        default => 'danger',
                                    };
                                @endphp

                                <span class="badge bg-{{ $badge }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td>
                                <a
                                    href="{{ route('purchases.show', $purchase) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                No purchases found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body">
            {{ $purchases->links() }}
        </div>
    </div>

</div>
@endsection
