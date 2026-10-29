<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date filter
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->input(
            'from',
            Carbon::now()->startOfMonth()->toDateString()
        );

        $to = $request->input(
            'to',
            Carbon::today()->toDateString()
        );

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        // Basic totals
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();

        // Sales
        $salesToday = Sale::whereDate('created_at', $today)
            ->sum('total');

        $salesThisMonth = Sale::whereBetween('created_at', [
            $monthStart,
            Carbon::now()->endOfMonth(),
        ])->sum('total');

        $totalSales = Sale::sum('total');

        // Purchases
        $purchasesToday = Purchase::whereDate('created_at', $today)
            ->sum('total');

        $purchasesThisMonth = Purchase::whereBetween('created_at', [
            $monthStart,
            Carbon::now()->endOfMonth(),
        ])->sum('total');

        $totalPurchases = Purchase::sum('total');

        // Low stock products
        $lowStockProducts = Product::whereColumn(
            'quantity',
            '<=',
            'min_stock'
        )
            ->orderBy('quantity')
            ->take(10)
            ->get();

        $lowStockCount = Product::whereColumn(
            'quantity',
            '<=',
            'min_stock'
        )->count();

        // Finance: selected date range
        $financeSales = (float) Sale::whereDate(
            'created_at', '>=', $from
        )->whereDate(
            'created_at', '<=', $to
        )->sum('total');

        $financeCost = (float) DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereDate('sales.created_at', '>=', $from)
            ->whereDate('sales.created_at', '<=', $to)
            ->selectRaw(
                'COALESCE(SUM(sale_items.quantity * sale_items.cost_price), 0) as amount'
            )
            ->value('amount');

        $financeExpenses = (float) DB::table('expenses')
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->sum('amount');

        $grossProfit = $financeSales - $financeCost;
        $netProfit = $grossProfit - $financeExpenses;

        // Today's expenses
        $expensesToday = (float) DB::table('expenses')
            ->whereDate('expense_date', $today)
            ->sum('amount');

        // This month's expenses
        $expensesThisMonth = (float) DB::table('expenses')
            ->whereBetween('expense_date', [
                $monthStart->toDateString(),
                Carbon::now()->toDateString(),
            ])
            ->sum('amount');

        // All-time expenses
        $totalExpenses = (float) DB::table('expenses')
            ->sum('amount');

        // Monthly sales and purchases for current year
        $monthlySales = Sale::selectRaw(
            'MONTH(created_at) as month, SUM(total) as amount'
        )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('amount', 'month');

        $monthlyPurchases = Purchase::selectRaw(
            'MONTH(created_at) as month, SUM(total) as amount'
        )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('amount', 'month');

        // Monthly operating expenses
        $monthlyExpenses = DB::table('expenses')
            ->selectRaw(
                'MONTH(expense_date) as month, SUM(amount) as amount'
            )
            ->whereYear('expense_date', Carbon::now()->year)
            ->groupByRaw('MONTH(expense_date)')
            ->pluck('amount', 'month');

        // Monthly cost of goods sold
        $monthlyCosts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->selectRaw(
                'MONTH(sales.created_at) as month,
                 SUM(sale_items.quantity * sale_items.cost_price) as amount'
            )
            ->whereYear('sales.created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(sales.created_at)')
            ->pluck('amount', 'month');

        // Build 12-month chart data
        $monthlyReport = collect(range(1, 12))->map(
            function ($month) use (
                $monthlySales,
                $monthlyPurchases,
                $monthlyExpenses,
                $monthlyCosts
            ) {
                $sales = (float) ($monthlySales[$month] ?? 0);
                $purchases = (float) ($monthlyPurchases[$month] ?? 0);
                $expenses = (float) ($monthlyExpenses[$month] ?? 0);
                $cost = (float) ($monthlyCosts[$month] ?? 0);

                $gross = $sales - $cost;

                return [
                    'month' => Carbon::create()->month($month)->format('M'),
                    'sales' => $sales,
                    'purchases' => $purchases,
                    'expenses' => $expenses,
                    'grossProfit' => $gross,
                    'netProfit' => $gross - $expenses,
                ];
            }
        );

        // Latest records
        $recentSales = Sale::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $recentPurchases = Purchase::with('supplier')
            ->latest()
            ->take(5)
            ->get();

        // Latest expenses
        $recentExpenses = DB::table('expenses')
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalCustomers',
            'totalSuppliers',
            'salesToday',
            'salesThisMonth',
            'totalSales',
            'purchasesToday',
            'purchasesThisMonth',
            'totalPurchases',
            'lowStockProducts',
            'lowStockCount',
            'monthlyReport',
            'recentSales',
            'recentPurchases',
            'from',
            'to',
            'financeSales',
            'financeCost',
            'financeExpenses',
            'grossProfit',
            'netProfit',
            'expensesToday',
            'expensesThisMonth',
            'totalExpenses',
            'recentExpenses'
        ));
    }
}
