
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <h2>Dashboard</h2>
        <p class="text-muted">
            Business overview — {{ now()->format('d M Y') }}
        </p>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted">Total Products</div>
                    <h3 class="mt-2 mb-0">
                        {{ number_format($totalProducts) }}
                    </h3>
                    <small class="text-muted">Products in database</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted">Total Customers</div>
                    <h3 class="mt-2 mb-0">
                        {{ number_format($totalCustomers) }}
                    </h3>
                    <small class="text-muted">Registered customers</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted">Sales Today</div>
                    <h3 class="mt-2 mb-0 text-success">
                        ${{ number_format((float) $salesToday, 2) }}
                    </h3>
                    <small class="text-muted">
                        This month: ${{ number_format((float) $salesThisMonth, 2) }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted">Purchases Today</div>
                    <h3 class="mt-2 mb-0 text-primary">
                        ${{ number_format((float) $purchasesToday, 2) }}
                    </h3>
                    <small class="text-muted">
                        This month: ${{ number_format((float) $purchasesThisMonth, 2) }}
                    </small>
                </div>
            </div>
        </div>

    </div>

    {{-- Totals --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">All-Time Sales</div>
                    <h4 class="text-success mt-2">
                        ${{ number_format((float) $totalSales, 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">All-Time Purchases</div>
                    <h4 class="text-primary mt-2">
                        ${{ number_format((float) $totalPurchases, 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Low Stock Products</div>
                    <h4 class="text-danger mt-2">
                        {{ number_format($lowStockCount) }}
                    </h4>
                    <a href="{{ route('products.index') }}" class="small">
                        View Products
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Monthly Report --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">
            Monthly Sales & Purchases — {{ now()->format('Y') }}
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th class="text-end">Sales</th>
                        <th class="text-end">Purchases</th>
                        <th class="text-end">Difference</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($monthlyReport as $report)
                        <tr>
                            <td>{{ $report['month'] }}</td>
                            <td class="text-end text-success">
                                ${{ number_format($report['sales'], 2) }}
                            </td>
                            <td class="text-end text-primary">
                                ${{ number_format($report['purchases'], 2) }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($report['sales'] - $report['purchases'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Low Stock Table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Low Stock Alert</strong>
            <span class="badge bg-danger">{{ $lowStockCount }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th class="text-end">Current Stock</th>
                        <th class="text-end">Minimum Stock</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($lowStockProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku ?? '—' }}</td>
                            <td class="text-end">
                                <span class="badge bg-danger">
                                    {{ number_format($product->quantity) }}
                                </span>
                            </td>
                            <td class="text-end">
                                {{ number_format($product->min_stock) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3">
                                No low-stock products.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Sales --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Recent Sales</div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($recentSales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                            <td>{{ $sale->created_at?->format('d/m/Y') }}</td>
                            <td class="text-end">
                                ${{ number_format((float) $sale->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3">
                                No sales recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Purchases --}}
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Recent Purchases</div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Purchase No.</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($recentPurchases as $purchase)
                        <tr>
                            <td>{{ $purchase->purchase_no }}</td>
                            <td>{{ $purchase->supplier?->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->created_at?->format('d/m/Y') }}</td>
                            <td class="text-end">
                                ${{ number_format((float) $purchase->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3">
                                No purchases recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
