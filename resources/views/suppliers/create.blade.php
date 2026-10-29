@extends('layouts.app')

@section('title', 'Add Supplier')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold mb-1">Add Supplier</h2>
        <p class="text-muted mb-0">Create a new supplier</p>
    </div>

    <a href="{{ route('suppliers.index') }}"
       class="btn btn-secondary">

        <i class="bi bi-arrow-left"></i>
        Back

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <form action="{{ route('suppliers.store') }}"
              method="POST">

            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Supplier Name
                        <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           required>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Company
                    </label>

                    <input type="text"
                           name="company"
                           class="form-control"
                           value="{{ old('company') }}">

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Phone
                    </label>

                    <input type="text"
                           name="phone"
                           class="form-control"
                           value="{{ old('phone') }}">

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email') }}">

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Opening Balance
                    </label>

                    <input type="number"
                           name="balance"
                           class="form-control"
                           value="{{ old('balance', 0) }}"
                           min="0"
                           step="0.01">

                </div>


                <div class="col-12 mb-3">

                    <label class="form-label">
                        Address
                    </label>

                    <textarea name="address"
                              class="form-control"
                              rows="3">{{ old('address') }}</textarea>

                </div>


                <div class="col-12 mb-3">

                    <div class="form-check">

                        <input type="checkbox"
                               name="status"
                               value="1"
                               id="status"
                               class="form-check-input"
                               checked>

                        <label class="form-check-label"
                               for="status">

                            Active

                        </label>

                    </div>

                </div>

            </div>


            <button type="submit"
                    class="btn btn-primary">

                <i class="bi bi-save"></i>
                Save Supplier

            </button>

        </form>

    </div>

</div>

@endsection