
@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <h2>Business Reports</h2>
        <p class="text-muted">Sales and purchase reports by date</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Filter by Date</div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="from" class="form-label">From Date</label>
                        <input
                            type="date"
                            id="from"
                            name="from"
                            class="form-control"
                            value="{{ $from }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="to" class="form-label">To Date</label>
                        <input
                            type="date"
                            id="to"
                            name="to"
                            class="form-control"
                            value="{{ $to }}"
                            required
                        >
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Generate Report
                        </button>

                        <a href="{{ route('reports.index') }}"
                           class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <a href="{{ route('reports.export.excel', [
                            'from' => $from,
                            'to' => $to
                        ]) }}" class="btn btn-success">
                            Export Excel
                        </a>

                        <a href="{{ route('reports.export.pdf', [
                            'from' => $from,
                            'to' => $to
                        ]) }}" class="btn btn-danger">
                            Export PDF
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Sales Total</div>
                    <h4 class="text-success mt-2">
                        ${{ number_format((float) $salesTotal, 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Sales Paid</div>
                    <h4 class="mt-2">
                        ${{ number_format((float) $salesPaid, 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Purchases Total</div>
                    <h4 class="text-primary mt-2">
                        ${{ number_format((float) $purchasesTotal, 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Purchases Paid</div>
                    <h4 class="mt-2">
                        ${{ number_format((float) $purchasesPaid, 2) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Sales Report</h4>
        <button onclick="window.print()" class="btn btn-outline-dark">
            Print / Save PDF
        </button>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice No.</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Change</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                            <td>{{ $sale->created_at?->format('d/m/Y') }}</td>
                            <td class="text-end">
                                {{ number_format((float) $sale->total, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format((float) $sale->paid, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format((float) ($sale->change ?? 0), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3">
                                No sales found for this date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $sales->links() }}</div>
    </div>

    <h4 class="mb-3">Purchase Report</h4>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Purchase No.</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->purchase_no }}</td>
                            <td>{{ $purchase->supplier?->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->created_at?->format('d/m/Y') }}</td>
                            <td class="text-end">
                                {{ number_format((float) $purchase->total, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format((float) $purchase->paid, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format((float) $purchase->due, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3">
                                No purchases found for this date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $purchases->links() }}</div>
    </div>

</div>

<style>
@media print {
    nav, aside, .sidebar, .btn, form, .pagination {
        display: none !important;
    }

    .card {
        box-shadow: none !important;
    }
}
</style>
@endsection
