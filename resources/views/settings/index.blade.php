
@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">System Settings</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Company & Invoice Information</strong>
        </div>

        <div class="card-body">
            <form action="{{ route('settings.update') }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Company Name</label>
                    <input type="text"
                           name="company_name"
                           class="form-control"
                           value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text"
                           name="phone"
                           class="form-control"
                           value="{{ old('phone', $settings['phone'] ?? '') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address"
                              class="form-control"
                              rows="3">{{ old('address', $settings['address'] ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Currency</label>
                    <select name="currency" class="form-select" required>
                        <option value="USD"
                            @selected(old('currency', $settings['currency'] ?? 'USD') === 'USD')>
                            USD - US Dollar
                        </option>
                        <option value="KHR"
                            @selected(old('currency', $settings['currency'] ?? 'USD') === 'KHR')>
                            KHR - Cambodian Riel
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Invoice Prefix</label>
                    <input type="text"
                           name="invoice_prefix"
                           class="form-control"
                           value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV') }}"
                           required>
                    <small class="text-muted">
                        Example: INV-0001
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Save Settings
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
