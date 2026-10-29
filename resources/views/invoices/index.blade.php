@extends('layouts.app')

@section('title', 'Invoices')

@section('content')

<div class="container">

    {{-- =========================
         HEADER
    ========================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="mb-1">
                Invoices
            </h2>

            <p class="text-muted mb-0">
                Manage sales invoices
            </p>

        </div>


        <a href="{{ route('sales.create') }}"
           class="btn btn-primary">

            + New Sale

        </a>

    </div>


    {{-- =========================
         SUCCESS MESSAGE
    ========================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =========================
         SEARCH & FILTER
    ========================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('invoices.index') }}">

                <div class="row g-3">


                    {{-- SEARCH --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Invoice No or Customer"
                               value="{{ request('search') }}">

                    </div>


                    {{-- PAYMENT --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            Payment
                        </label>

                        <select name="payment_method"
                                class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="cash"
                                {{ request('payment_method') == 'cash' ? 'selected' : '' }}>
                                Cash
                            </option>

                            <option value="card"
                                {{ request('payment_method') == 'card' ? 'selected' : '' }}>
                                Card
                            </option>

                            <option value="bank"
                                {{ request('payment_method') == 'bank' ? 'selected' : '' }}>
                                Bank
                            </option>

                            <option value="qr"
                                {{ request('payment_method') == 'qr' ? 'selected' : '' }}>
                                QR
                            </option>

                        </select>

                    </div>


                    {{-- FROM DATE --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input type="date"
                               name="from_date"
                               class="form-control"
                               value="{{ request('from_date') }}">

                    </div>


                    {{-- TO DATE --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input type="date"
                               name="to_date"
                               class="form-control"
                               value="{{ request('to_date') }}">

                    </div>


                    {{-- BUTTONS --}}
                    <div class="col-md-2 d-flex align-items-end gap-2">

                        <button type="submit"
                                class="btn btn-primary">

                            Search

                        </button>


                        <a href="{{ route('invoices.index') }}"
                           class="btn btn-secondary">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================
         INVOICE TABLE
    ========================== --}}

    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-bordered mb-0">

                    <thead class="table-dark">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Invoice
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Date
                            </th>

                            <th class="text-end">
                                Total
                            </th>

                            <th class="text-end">
                                Paid
                            </th>

                            <th class="text-end">
                                Change
                            </th>

                            <th>
                                Payment
                            </th>

                            <th class="text-center">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($sales as $index => $sale)

                            <tr>

                                {{-- NUMBER --}}
                                <td>

                                    {{ $sales->firstItem() + $index }}

                                </td>


                                {{-- INVOICE --}}
                                <td>

                                    <strong>
                                        {{ $sale->invoice_no }}
                                    </strong>

                                </td>


                                {{-- CUSTOMER --}}
                                <td>

                                    {{ $sale->customer?->name
                                        ?? 'Walk-in Customer' }}

                                </td>


                                {{-- DATE --}}
                                <td>

                                    {{ $sale->created_at
                                        ? $sale->created_at->format('Y-m-d H:i')
                                        : '-' }}

                                </td>


                                {{-- TOTAL --}}
                                <td class="text-end">

                                    ${{ number_format(
                                        $sale->total ?? 0,
                                        2
                                    ) }}

                                </td>


                                {{-- PAID --}}
                                <td class="text-end">

                                    ${{ number_format(
                                        $sale->paid ?? 0,
                                        2
                                    ) }}

                                </td>


                                {{-- CHANGE --}}
                                <td class="text-end">

                                    ${{ number_format(
                                        $sale->change ?? 0,
                                        2
                                    ) }}

                                </td>


                                {{-- PAYMENT --}}
                                <td>

                                    <span class="badge bg-secondary">

                                        {{ ucfirst(
                                            $sale->payment_method ?? 'cash'
                                        ) }}

                                    </span>

                                </td>


                                {{-- ACTION --}}
                                <td class="text-center">

                                    <div class="d-flex justify-content-center gap-1">


                                        {{-- VIEW --}}
                                        <a href="{{ route('sales.show', $sale->id) }}"
                                           class="btn btn-sm btn-info text-white"
                                           title="View">

                                            👁

                                        </a>


                                        {{-- EDIT --}}
                                        <a href="{{ route('sales.edit', $sale->id) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Edit">

                                            ✏

                                        </a>


                                        {{-- PRINT --}}
                                        <a href="{{ route('sales.show', $sale->id) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-secondary"
                                           title="Print">

                                            🖨

                                        </a>


                                        {{-- DELETE --}}
                                        <form action="{{ route('sales.destroy', $sale->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this invoice?');">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete">

                                                🗑

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9"
                                    class="text-center py-4 text-muted">

                                    No invoices found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =========================
             PAGINATION
        ========================== --}}

        @if($sales->hasPages())

            <div class="card-footer">

                {{ $sales->links() }}

            </div>

        @endif

    </div>


    {{-- RESULT INFORMATION --}}

    @if($sales->total() > 0)

        <div class="mt-3 text-muted">

            Showing
            <strong>
                {{ $sales->firstItem() }}
            </strong>

            to

            <strong>
                {{ $sales->lastItem() }}
            </strong>

            of

            <strong>
                {{ $sales->total() }}
            </strong>

            invoices.

        </div>

    @endif

</div>

@endsection