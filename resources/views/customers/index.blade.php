@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold mb-1">Customers</h2>
        <p class="text-muted mb-0">
            Manage your customers
        </p>
    </div>

    <a href="{{ route('customers.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-lg"></i>
        Add Customer

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <form method="GET"
              action="{{ route('customers.index') }}"
              class="row g-2 mb-4">

            <div class="col-md-10">

                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search name, phone or email..."
                       value="{{ $search }}">

            </div>

            <div class="col-md-2">

                <button class="btn btn-dark w-100">

                    <i class="bi bi-search"></i>
                    Search

                </button>

            </div>

        </form>


        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th width="70">ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th width="210">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($customers as $customer)

                        <tr>

                            <td>
                                {{ $customer->id }}
                            </td>

                            <td class="fw-semibold">
                                {{ $customer->name }}
                            </td>

                            <td>
                                {{ $customer->phone ?? '-' }}
                            </td>

                            <td>
                                {{ $customer->email ?? '-' }}
                            </td>

                            <td>
                                ${{ number_format($customer->balance, 2) }}
                            </td>

                            <td>

                                @if($customer->status)

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

                                <a href="{{ route('customers.show', $customer) }}"
                                   class="btn btn-info btn-sm">

                                    <i class="bi bi-eye"></i>

                                </a>

                                <a href="{{ route('customers.edit', $customer) }}"
                                   class="btn btn-warning btn-sm">

                                    <i class="bi bi-pencil"></i>
                                    Edit

                                </a>

                                <form action="{{ route('customers.destroy', $customer) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this customer?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center py-4 text-muted">

                                No customers found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="mt-3">

            {{ $customers->links() }}

        </div>

    </div>

</div>

@endsection