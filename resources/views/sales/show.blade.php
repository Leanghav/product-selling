@extends('layouts.app')

@section('title', 'Invoice ' . $sale->invoice_no)

@section('content')

<div class="container">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">

        <h2 class="mb-0">
            Invoice
        </h2>

        <div class="d-flex gap-2">

            {{-- BACK TO INVOICES --}}
            <a href="{{ route('invoices.index') }}"
               class="btn btn-secondary">
                ← Back
            </a>

            {{-- PRINT --}}
            <button type="button"
                    onclick="window.print()"
                    class="btn btn-primary">
                🖨 Print
            </button>

        </div>

    </div>


    {{-- INVOICE CARD --}}
    <div class="card shadow-sm">

        <div class="card-body p-4">

            {{-- =========================
                 INVOICE HEADER
            ========================== --}}

            <div class="row mb-4">

                {{-- COMPANY --}}
                <div class="col-md-6">

                    <h3 class="fw-bold mb-1">
                        PRODUCT SELLING
                    </h3>

                    <p class="mb-0 text-muted">
                        Sales Invoice
                    </p>

                </div>


                {{-- INVOICE INFORMATION --}}
                <div class="col-md-6 text-md-end">

                    <h5 class="mb-2">

                        Invoice:
                        <strong>
                            {{ $sale->invoice_no }}
                        </strong>

                    </h5>

                    <p class="mb-1">

                        Date:

                        {{ $sale->created_at
                            ? $sale->created_at->format('Y-m-d H:i')
                            : '-' }}

                    </p>

                </div>

            </div>


            <hr>


            {{-- =========================
                 CUSTOMER INFORMATION
            ========================== --}}

            <div class="row mb-4">

                {{-- CUSTOMER --}}
                <div class="col-md-6">

                    <strong>
                        Customer:
                    </strong>

                    <br>

                    {{ $sale->customer->name ?? 'Walk-in Customer' }}

                </div>


                {{-- PAYMENT --}}
                <div class="col-md-6 text-md-end">

                    <strong>
                        Payment:
                    </strong>

                    <br>

                    {{ ucfirst($sale->payment_method ?? 'cash') }}

                </div>

            </div>


            {{-- =========================
                 ITEMS
            ========================== --}}

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th style="width: 60px;">
                                #
                            </th>

                            <th>
                                Product
                            </th>

                            <th class="text-center"
                                style="width: 100px;">
                                Qty
                            </th>

                            <th class="text-end"
                                style="width: 150px;">
                                Price
                            </th>

                            <th class="text-end"
                                style="width: 150px;">
                                Total
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($sale->items as $index => $item)

                            <tr>

                                {{-- NUMBER --}}
                                <td>
                                    {{ $index + 1 }}
                                </td>


                                {{-- PRODUCT --}}
                                <td>

                                    {{ $item->product->name
                                        ?? 'Deleted Product' }}

                                </td>


                                {{-- QUANTITY --}}
                                <td class="text-center">

                                    {{ $item->quantity }}

                                </td>


                                {{-- PRICE --}}
                                <td class="text-end">

                                    ${{ number_format(
                                        $item->price ?? 0,
                                        2
                                    ) }}

                                </td>


                                {{-- TOTAL --}}
                                <td class="text-end">

                                    ${{ number_format(
                                        $item->total ?? 0,
                                        2
                                    ) }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center text-muted">

                                    No items found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =========================
                 TOTAL
            ========================== --}}

            <div class="row justify-content-end">

                <div class="col-md-5">

                    <table class="table table-sm">


                        {{-- SUBTOTAL --}}
                        <tr>

                            <th>
                                Subtotal
                            </th>

                            <td class="text-end">

                                ${{ number_format(
                                    $sale->subtotal ?? 0,
                                    2
                                ) }}

                            </td>

                        </tr>


                        {{-- DISCOUNT --}}
                        <tr>

                            <th>
                                Discount
                            </th>

                            <td class="text-end">

                                ${{ number_format(
                                    $sale->discount ?? 0,
                                    2
                                ) }}

                            </td>

                        </tr>


                        {{-- TAX --}}
                        <tr>

                            <th>
                                Tax
                            </th>

                            <td class="text-end">

                                ${{ number_format(
                                    $sale->tax ?? 0,
                                    2
                                ) }}

                            </td>

                        </tr>


                        {{-- TOTAL --}}
                        <tr class="table-dark">

                            <th>
                                TOTAL
                            </th>

                            <th class="text-end">

                                ${{ number_format(
                                    $sale->total ?? 0,
                                    2
                                ) }}

                            </th>

                        </tr>


                        {{-- PAID --}}
                        <tr>

                            <th>
                                Paid
                            </th>

                            <td class="text-end">

                                ${{ number_format(
                                    $sale->paid ?? 0,
                                    2
                                ) }}

                            </td>

                        </tr>


                        {{-- CHANGE --}}
                        <tr>

                            <th>
                                Change
                            </th>

                            <td class="text-end">

                                ${{ number_format(
                                    $sale->change ?? 0,
                                    2
                                ) }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>


            {{-- =========================
                 NOTE
            ========================== --}}

            @if($sale->note)

                <div class="mt-3">

                    <strong>
                        Note:
                    </strong>

                    <p class="mb-0">
                        {{ $sale->note }}
                    </p>

                </div>

            @endif


            <hr>


            {{-- =========================
                 FOOTER
            ========================== --}}

            <div class="text-center">

                <p class="mb-0">
                    Thank you for your business!
                </p>

            </div>

        </div>

    </div>

</div>


{{-- =========================
     PRINT CSS
========================== --}}

<style>

@media print {

    /* Hide everything first */
    body * {
        visibility: hidden;
    }


    /* Show invoice */
    .card,
    .card * {
        visibility: visible;
    }


    /* Invoice position */
    .card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;

        border: none !important;
        box-shadow: none !important;
    }


    /* Hide buttons/header */
    .no-print {
        display: none !important;
    }


    /* Hide links/buttons */
    .btn,
    a {
        display: none !important;
    }


    /* Remove container spacing */
    .container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }


    /* Better print table */
    table {
        width: 100% !important;
    }


    /* Remove unnecessary shadows */
    .shadow-sm {
        box-shadow: none !important;
    }


    /* Avoid page breaks inside rows */
    tr {
        page-break-inside: avoid;
    }

}

</style>

@endsection