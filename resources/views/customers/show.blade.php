@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2 class="fw-bold">
        Customer Details
    </h2>

    <a href="{{ route('customers.index') }}"
       class="btn btn-secondary">

        <i class="bi bi-arrow-left"></i>
        Back

    </a>

</div>


<div class="row g-4">

    <div class="col-lg-4">

        <div class="card shadow-sm">

            <div class="card-body">

                <h4 class="fw-bold">
                    {{ $customer->name }}
                </h4>

                <hr>

                <p>
                    <strong>Phone:</strong><br>
                    {{ $customer->phone ?? '-' }}
                </p>

                <p>
                    <strong>Email:</strong><br>
                    {{ $customer->email ?? '-' }}
                </p>

                <p>
                    <strong>Address:</strong><br>
                    {{ $customer->address ?? '-' }}
                </p>

                <p>
                    <strong>Balance:</strong><br>

                    ${{ number_format($customer->balance, 2) }}

                </p>

                <p>
                    <strong>Status:</strong>

                    @if($customer->status)

                        <span class="badge bg-success">
                            Active
                        </span>

                    @else

                        <span class="badge bg-secondary">
                            Inactive
                        </span>

                    @endif

                </p>

            </div>

        </div>

    </div>


    <div class="col-lg-8">

        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Sales History
                </h5>

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead class="table-dark">

                            <tr>

                                <th>Invoice</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Payment</th>
                                <th>Date</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($customer->sales as $sale)

                                <tr>

                                    <td>
                                        {{ $sale->invoice_no }}
                                    </td>

                                    <td>
                                        ${{ number_format($sale->total, 2) }}
                                    </td>

                                    <td>
                                        ${{ number_format($sale->paid, 2) }}
                                    </td>

                                    <td>
                                        {{ ucfirst($sale->payment_method) }}
                                    </td>

                                    <td>
                                        {{ $sale->created_at->format('d/m/Y H:i') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5"
                                        class="text-center text-muted">

                                        No sales history.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection