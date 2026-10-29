<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $salesQuery = Sale::query()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        $purchasesQuery = Purchase::query()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        $salesTotal = (clone $salesQuery)->sum('total');
        $salesPaid = (clone $salesQuery)->sum('paid');
        $purchasesTotal = (clone $purchasesQuery)->sum('total');
        $purchasesPaid = (clone $purchasesQuery)->sum('paid');

        $sales = (clone $salesQuery)
            ->with('customer')
            ->latest()
            ->paginate(10, ['*'], 'sales_page')
            ->withQueryString();

        $purchases = (clone $purchasesQuery)
            ->with('supplier')
            ->latest()
            ->paginate(10, ['*'], 'purchases_page')
            ->withQueryString();

        return view('reports.index', compact(
            'from',
            'to',
            'salesTotal',
            'salesPaid',
            'purchasesTotal',
            'purchasesPaid',
            'sales',
            'purchases'
        ));
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        return Excel::download(
            new SalesReportExport($request->from, $request->to),
            'sales-report.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $sales = \App\Models\Sale::with('customer')
            ->whereDate('created_at', '>=', $request->from)
            ->whereDate('created_at', '<=', $request->to)
            ->latest()
            ->get();

        $total = $sales->sum('total');
        $paid = $sales->sum('paid');

        $pdf = Pdf::loadView('reports.pdf', [
            'sales' => $sales,
            'from' => $request->from,
            'to' => $request->to,
            'total' => $total,
            'paid' => $paid,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('sales-report.pdf');
    }
}