<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProfitReportExport implements FromArray, ShouldAutoSize, WithTitle
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function title(): string
    {
        return 'Profit Report';
    }

    public function array(): array
    {
        $sales = DB::table('sales as s')
            ->join('sale_items as si', 'si.sale_id', '=', 's.id')
            ->whereDate('s.created_at', '>=', $this->from)
            ->whereDate('s.created_at', '<=', $this->to)
            ->select(
                's.id',
                's.invoice_no',
                's.created_at'
            )
            ->selectRaw('SUM(si.quantity * si.price) as revenue')
            ->selectRaw('SUM(si.quantity * si.cost_price) as cost')
            ->groupBy('s.id', 's.invoice_no', 's.created_at')
            ->orderBy('s.created_at')
            ->get();

        $expenses = DB::table('expenses')
            ->whereDate('expense_date', '>=', $this->from)
            ->whereDate('expense_date', '<=', $this->to)
            ->orderBy('expense_date')
            ->get();

        $totalRevenue = $sales->sum('revenue');
        $totalCost = $sales->sum('cost');
        $grossProfit = $totalRevenue - $totalCost;
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $grossProfit - $totalExpenses;

        $rows = [
            ['PROFIT REPORT'],
            ['From', $this->from, 'To', $this->to],
            [],
            ['Invoice No', 'Date', 'Revenue', 'Cost', 'Gross Profit'],
        ];

        foreach ($sales as $sale) {
            $rows[] = [
                $sale->invoice_no ?? ('SALE-' . $sale->id),
                $sale->created_at,
                (float) $sale->revenue,
                (float) $sale->cost,
                (float) $sale->revenue - (float) $sale->cost,
            ];
        }

        $rows[] = [
            'TOTAL SALES',
            '',
            $totalRevenue,
            $totalCost,
            $grossProfit,
        ];

        $rows[] = [];
        $rows[] = ['EXPENSES'];
        $rows[] = ['Date', 'Category', 'Name', 'Amount'];

        foreach ($expenses as $expense) {
            $rows[] = [
                $expense->expense_date,
                $expense->category,
                $expense->name,
                (float) $expense->amount,
            ];
        }

        $rows[] = ['TOTAL EXPENSES', '', '', $totalExpenses];
        $rows[] = [];
        $rows[] = ['NET PROFIT', '', '', '', $netProfit];

        return $rows;
    }
}
