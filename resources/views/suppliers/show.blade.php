@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold mb-1">Supplier Details</h2>
        <p class="text-muted mb-0">
            Supplier information and purchase history
        </p>
    </div>

    <a href="{{ route('suppliers.index') }}"
       class="btn btn-secondary">

        <i class="bi bi-arrow-left"></i>
        Back

    </a>

</div>


<div class="row g-4">

    {{-- Supplier Information --}}

    <div class="col-lg-4">

        <div class="card shadow-sm">

            <div class="card-body">

                <div class="text-center mb-4">

                    <div class="display-4 text-primary">

                        <i class="bi bi-truck"></i>

                    </div>

                    <h4 class="fw-bold mt-2">
                        {{ $supplier->name }}
                    </h4>

                    @if($supplier->status)

                        <span class="badge bg-success">
                            Active
                        </span>

                    @else

                        <span class="badge bg-secondary">
                            Inactive
                        </span>

                    @endif

                </div>

                <hr>

                <p>
                    <strong>Company</strong><br>
                    {{ $supplier->company ?? '-' }}
                </p>

                <p>
                    <strong>Phone</strong><br>
                    {{ $supplier->phone ?? '-' }}
                </p>

                <p>
                    <strong>Email</strong><br>
                    {{ $supplier->email ?? '-' }}
                </p>

                <p>
                    <strong>Address</strong><br>
                    {{ $supplier->address ?? '-' }}
                </p>

                <p class="mb-0">

                    <strong>Balance</strong><br>

                    <span class="fs-5">
                        ${{ number_format($supplier->balance, 2) }}
                    </span>

                </p>

            </div>

        </div>

    </div>


    {{-- Purchase History --}}

    <div class="col-lg-8">

        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="fw-bold mb-3">

                    <i class="bi bi-receipt"></i>

                    Purchase History

                </h5>


                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-dark">

                            <tr>

                                <th>Purchase No</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Date</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($supplier->purchases as $purchase)

                                <tr>

                                    <td>
                                        {{ $purchase->purchase_no }}
                                    </td>

                                    <td>
                                        ${{ number_format($purchase->total, 2) }}
                                    </td>

                                    <td>
                                        ${{ number_format($purchase->paid, 2) }}
                                    </td>

                                    <td>
                                        ${{ number_format($purchase->due, 2) }}
                                    </td>

                                    <td>
                                        {{ $purchase->created_at->format('d/m/Y H:i') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5"
                                        class="text-center text-muted py-4">

                                        No purchase history.

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