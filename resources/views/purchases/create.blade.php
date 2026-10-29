
@extends('layouts.app')

@section('title', 'New Purchase')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>New Purchase</h2>
            <p class="text-muted mb-0">Record a new supplier purchase</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
            Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">Purchase Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="supplier_id" class="form-label">Supplier *</label>
                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option
                                    value="{{ $supplier->id }}"
                                    @selected(old('supplier_id') == $supplier->id)
                                >
                                    {{ $supplier->name }}
                                    @if ($supplier->company)
                                        - {{ $supplier->company }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @if ($suppliers->isEmpty())
                            <small class="text-danger">
                                No active suppliers found. Please add or activate a supplier first.
                            </small>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="note" class="form-label">Note</label>
                        <input
                            type="text"
                            name="note"
                            id="note"
                            class="form-control"
                            value="{{ old('note') }}"
                            maxlength="2000"
                            placeholder="Optional note"
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">Purchase Items</div>
            <div class="card-body">
                @if ($products->isEmpty())
                    <div class="alert alert-warning">
                        No active products found. Please add or activate products first.
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width:220px">Product</th>
                                <th style="min-width:120px">Quantity</th>
                                <th style="min-width:150px">Cost Price</th>
                                <th style="min-width:150px">Subtotal</th>
                                <th style="width:70px">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][product_id]" class="form-select product-select" required>
                                        <option value="">-- Select Product --</option>
                                        @foreach ($products as $product)
                                            <option
                                                value="{{ $product->id }}"
                                                data-cost="{{ $product->cost_price }}"
                                            >
                                                {{ $product->name }}
                                                @if ($product->sku)
                                                    ({{ $product->sku }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="items[0][quantity]"
                                        class="form-control quantity-input"
                                        min="1"
                                        step="1"
                                        value="1"
                                        required
                                    >
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="items[0][cost_price]"
                                        class="form-control cost-input"
                                        min="0"
                                        step="0.01"
                                        value="0.00"
                                        required
                                    >
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal-output" value="0.00" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger remove-row" title="Remove item">
                                        &times;
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary" id="addRow">
                    + Add Product
                </button>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">Payment Summary</div>
            <div class="card-body">
                <div class="row g-3 justify-content-end">
                    <div class="col-md-4">
                        <label class="form-label">Subtotal</label>
                        <input type="text" id="subtotalDisplay" class="form-control" value="0.00" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="discount" class="form-label">Discount</label>
                        <input
                            type="number"
                            name="discount"
                            id="discount"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="{{ old('discount', '0') }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Total</label>
                        <input type="text" id="totalDisplay" class="form-control fw-bold" value="0.00" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="paid" class="form-label">Paid Amount *</label>
                        <input
                            type="number"
                            name="paid"
                            id="paid"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="{{ old('paid', '0') }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Amount Due</label>
                        <input type="text" id="dueDisplay" class="form-control" value="0.00" readonly>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button
                        type="submit"
                        class="btn btn-primary"
                        @disabled($suppliers->isEmpty() || $products->isEmpty())
                    >
                        Save Purchase
                    </button>

                    <a href="{{ route('purchases.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemsBody = document.getElementById('itemsBody');
    const addRowButton = document.getElementById('addRow');
    const discountInput = document.getElementById('discount');
    const paidInput = document.getElementById('paid');

    let rowIndex = 1;

    function calculateTotals() {
        let subtotal = 0;

        document.querySelectorAll('.item-row').forEach(function (row) {
            const quantity = Math.max(
                0,
                parseFloat(row.querySelector('.quantity-input').value) || 0
            );

            const cost = Math.max(
                0,
                parseFloat(row.querySelector('.cost-input').value) || 0
            );

            const lineTotal = Math.round(quantity * cost * 100) / 100;

            row.querySelector('.subtotal-output').value = lineTotal.toFixed(2);
            subtotal += lineTotal;
        });

        subtotal = Math.round(subtotal * 100) / 100;

        const discount = Math.max(0, parseFloat(discountInput.value) || 0);
        const total = Math.max(0, Math.round((subtotal - discount) * 100) / 100);
        const paid = Math.max(0, parseFloat(paidInput.value) || 0);
        const due = Math.max(0, Math.round((total - paid) * 100) / 100);

        document.getElementById('subtotalDisplay').value = subtotal.toFixed(2);
        document.getElementById('totalDisplay').value = total.toFixed(2);
        document.getElementById('dueDisplay').value = due.toFixed(2);
    }

    function attachRowEvents(row) {
        row.querySelector('.product-select').addEventListener('change', function () {
            const option = this.options[this.selectedIndex];
            const costInput = row.querySelector('.cost-input');

            if (this.value && option.dataset.cost !== undefined) {
                costInput.value = option.dataset.cost || '0';
            }

            calculateTotals();
        });

        row.querySelector('.quantity-input').addEventListener('input', calculateTotals);
        row.querySelector('.cost-input').addEventListener('input', calculateTotals);

        row.querySelector('.remove-row').addEventListener('click', function () {
            if (itemsBody.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateTotals();
            }
        });
    }

    addRowButton.addEventListener('click', function () {
        const firstRow = itemsBody.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input').forEach(function (input) {
            if (input.classList.contains('quantity-input')) {
                input.value = '1';
            } else if (input.classList.contains('cost-input')) {
                input.value = '0.00';
            } else if (input.classList.contains('subtotal-output')) {
                input.value = '0.00';
            }
        });

        newRow.querySelectorAll('select').forEach(function (select) {
            select.selectedIndex = 0;
        });

        newRow.querySelectorAll('[name]').forEach(function (element) {
            element.name = element.name.replace(
                /items\[\d+\]/,
                'items[' + rowIndex + ']'
            );
        });

        rowIndex++;
        itemsBody.appendChild(newRow);
        attachRowEvents(newRow);
        calculateTotals();
    });

    discountInput.addEventListener('input', calculateTotals);
    paidInput.addEventListener('input', calculateTotals);

    itemsBody.querySelectorAll('.item-row').forEach(attachRowEvents);

    document.getElementById('purchaseForm').addEventListener('submit', function (event) {
        const subtotal = parseFloat(document.getElementById('subtotalDisplay').value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const paid = parseFloat(paidInput.value) || 0;
        const total = Math.max(0, subtotal - discount);

        if (discount > subtotal) {
            event.preventDefault();
            alert('Discount cannot exceed subtotal.');
            return;
        }

        if (paid > total) {
            event.preventDefault();
            alert('Paid amount cannot exceed total.');
        }
    });

    calculateTotals();
});
</script>
@endsection
