
@extends('layouts.app')

@section('title', 'Profit Report')

@section('content')
<div class="container-fluid py-4">

    <h2 class="fw-bold mb-4">Profit Report</h2>

    <form action="{{ route('reports.profit') }}"
          method="GET"
          class="row g-3 mb-4">

        <div class="col-md-4">
            <label class="form-label">ចាប់ពីថ្ងៃ</label>
            <input type="date"
                   name="from"
                   value="{{ $from }}"
                   class="form-control"
                   required>
        </div>

        <div class="col-md-4">
            <label class="form-label">ដល់ថ្ងៃ</label>
            <input type="date"
                   name="to"
                   value="{{ $to }}"
                   class="form-control"
                   min="{{ $from }}"
                   required>
        </div>

        <div class="col-md-4 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">
                ស្វែងរក
            </button>

            <a href="{{ route('reports.profit') }}"
               class="btn btn-secondary">
                Reset
            </a>
        </div>
    </form>

    @php
        $revenue = $report->sum('revenue');
        $cost = $report->sum('cost');
        $profit = $report->sum('profit');
    @endphp

    
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="card shadow-sm p-3 h-100">
            <div class="text-muted">ចំណូលសរុប</div>
            <h4 class="text-primary fw-bold mt-2">
                ${{ number_format($totalRevenue, 2) }}
            </h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm p-3 h-100">
            <div class="text-muted">ថ្លៃដើមទំនិញ</div>
            <h4 class="text-warning fw-bold mt-2">
                ${{ number_format($totalCost, 2) }}
            </h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm p-3 h-100">
            <div class="text-muted">ចំណាយផ្សេងៗ</div>
            <h4 class="text-danger fw-bold mt-2">
                ${{ number_format($totalExpenses, 2) }}
            </h4>
        </div>
    </div>

    <div class="col-md-3">
            <div class="card shadow-sm p-3 h-100">
                <div class="text-muted">ប្រាក់ចំណេញសុទ្ធ</div>
                <h4 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} fw-bold mt-2">
                    ${{ number_format($netProfit, 2) }}
                </h4>
            </div>
        </div>

    </div>
    


    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>ព័ត៌មានលម្អិត</h5>

        <a href="{{ route('reports.profit.excel', [
            'from' => $from,
            'to' => $to
        ]) }}"
           class="btn btn-success">
            Export Excel
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>កាលបរិច្ឆេទ</th>
                        <th class="text-end">ចំណូល</th>
                        <th class="text-end">ថ្លៃដើម</th>
                        <th class="text-end">ប្រាក់ចំណេញ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($report as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->invoice_no ?? ('SALE-' . $item->sale_id) }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                            <td class="text-end">
                                ${{ number_format($item->revenue, 2) }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($item->cost, 2) }}
                            </td>
                            <td class="text-end fw-bold">
                                ${{ number_format($item->profit, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                មិនមានទិន្នន័យលក់ក្នុងកាលបរិច្ឆេទនេះទេ។
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">សរុប</td>
                        <td class="text-end">
                            ${{ number_format($revenue, 2) }}
                        </td>
                        <td class="text-end">
                            ${{ number_format($cost, 2) }}
                        </td>
                        <td class="text-end">
                            ${{ number_format($profit, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <p class="text-muted small mt-3">
        ចំណាំ៖ ប្រាក់ចំណេញនេះមិនទាន់ដកចំណាយប្រតិបត្តិការ និង Discount
        កម្រិតវិក្កយបត្រទេ។
    </p>

</div>
@endsection
