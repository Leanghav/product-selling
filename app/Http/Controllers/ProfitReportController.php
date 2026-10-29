<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ProfitReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ProfitReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->input(
            'from',
            now()->startOfMonth()->toDateString()
        );

        $to = $request->input(
            'to',
            now()->toDateString()
        );

        $report = $this->getProfitData($from, $to);

        $expenses = DB::table('expenses')
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->orderByDesc('expense_date')
            ->get();

        $totalRevenue = $report->sum('revenue');
        $totalCost = $report->sum('cost');
        $grossProfit = $totalRevenue - $totalCost;
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $grossProfit - $totalExpenses;

        return view('reports.profit', compact(
            'from',
            'to',
            'report',
            'expenses',
            'totalRevenue',
            'totalCost',
            'grossProfit',
            'totalExpenses',
            'netProfit'
        ));
    }

    private function getProfitData($from, $to)
    {
        return DB::table('sales as s')
            ->join('sale_items as si', 'si.sale_id', '=', 's.id')
            ->whereDate('s.created_at', '>=', $from)
            ->whereDate('s.created_at', '<=', $to)
            ->select(
                's.id as sale_id',
                's.invoice_no',
                's.created_at'
            )
            ->selectRaw('
                COALESCE(SUM(si.quantity * si.price), 0) AS revenue,
                COALESCE(SUM(si.quantity * si.cost_price), 0) AS cost
            ')
            ->selectRaw('
                COALESCE(SUM(si.quantity * si.price), 0)
                - COALESCE(SUM(si.quantity * si.cost_price), 0)
                AS profit
            ')
            ->groupBy('s.id', 's.invoice_no', 's.created_at')
            ->orderByDesc('s.created_at')
            ->get();
    }

    public function exportExcel(Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = $validated['from'];
        $to = $validated['to'];

        $filename = "profit-report-{$from}-to-{$to}.xlsx";

        return Excel::download(
            new ProfitReportExport($from, $to),
            $filename
        );
    }
}

