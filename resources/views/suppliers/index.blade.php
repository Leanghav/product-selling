@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold mb-1">Suppliers</h2>

        <p class="text-muted mb-0">
            Manage your suppliers
        </p>
    </div>

    <a href="{{ route('suppliers.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-lg"></i>
        Add Supplier

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        {{-- Search --}}

        <form method="GET"
              action="{{ route('suppliers.index') }}"
              class="row g-2 mb-4">

            <div class="col-md-10">

                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search name, company, phone or email..."
                       value="{{ $search }}">

            </div>

            <div class="col-md-2">

                <button type="submit"
                        class="btn btn-dark w-100">

                    <i class="bi bi-search"></i>
                    Search

                </button>

            </div>

        </form>


        {{-- Table --}}

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th width="70">ID</th>

                        <th>Name</th>

                        <th>Company</th>

                        <th>Phone</th>

                        <th>Email</th>

                        <th>Balance</th>

                        <th>Status</th>

                        <th width="220">Action</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($suppliers as $supplier)

                        <tr>

                            <td>
                                {{ $supplier->id }}
                            </td>

                            <td class="fw-semibold">
                                {{ $supplier->name }}
                            </td>

                            <td>
                                {{ $supplier->company ?? '-' }}
                            </td>

                            <td>
                                {{ $supplier->phone ?? '-' }}
                            </td>

                            <td>
                                {{ $supplier->email ?? '-' }}
                            </td>

                            <td>
                                ${{ number_format($supplier->balance, 2) }}
                            </td>

                            <td>

                                @if($supplier->status)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td>

                                <a href="{{ route('suppliers.show', $supplier) }}"
                                   class="btn btn-info btn-sm">

                                    <i class="bi bi-eye"></i>

                                </a>


                                <a href="{{ route('suppliers.edit', $supplier) }}"
                                   class="btn btn-warning btn-sm">

                                    <i class="bi bi-pencil"></i>
                                    Edit

                                </a>


                                <form action="{{ route('suppliers.destroy', $supplier) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this supplier?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center py-4 text-muted">

                                No suppliers found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}

        <div class="mt-3">

            {{ $suppliers->links() }}

        </div>

    </div>

</div>

@endsection