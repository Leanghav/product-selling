<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        return Sale::with('customer')
            ->whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to)
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Customer',
            'Date',
            'Subtotal',
            'Discount',
            'Tax',
            'Total',
            'Paid',
            'Change',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->invoice_no,
            $sale->customer->name ?? 'Walk-in Customer',
            $sale->created_at?->format('Y-m-d'),
            $sale->subtotal,
            $sale->discount,
            $sale->tax,
            $sale->total,
            $sale->paid,
            $sale->change,
        ];
    }
}