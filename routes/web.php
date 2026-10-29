<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

use App\Http\Controllers\ProfitReportController;


// =========================
// Dashboard
// =========================

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');


// =========================
// Categories
// =========================

Route::resource('categories', CategoryController::class);


// =========================
// Products
// =========================

Route::resource('products', ProductController::class);


// =========================
// Customers
// =========================

Route::resource('customers', CustomerController::class);


// =========================
// Suppliers
// =========================

Route::resource('suppliers', SupplierController::class);


// =========================
// Sales / POS
// =========================

Route::resource('sales', SaleController::class);


// =========================
// Invoices
// =========================

Route::get('/invoices', [SaleController::class, 'invoices'])->name('invoices.index');


// =========================
// Purchases
// =========================

Route::resource('purchases', PurchaseController::class);


// =========================
// Stock
// =========================

Route::get('/stock-movements', [StockMovementController::class, 'index'])
    ->name('stock-movements.index');


//==================
//Dashboard
//=============
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


//============================
// Report 
//===========================
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/export/excel', [
    ReportController::class,
    'exportExcel'
])->name('reports.export.excel');

Route::get('/reports/export/pdf', [
    ReportController::class,
    'exportPdf'
])->name('reports.export.pdf');

Route::get('/reports/profit', [
    ProfitReportController::class,
    'index'
])->name('reports.profit');

Route::get('/reports/profit/export-excel', [
    ProfitReportController::class,
    'exportExcel'
])->name('reports.profit.excel');


//============================
// setting
//===========================

Route::get('/settings', [
    SettingController::class, 'index'
])->name('settings.index');

Route::put('/settings', [
    SettingController::class, 'update'
])->name('settings.update');