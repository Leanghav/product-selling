@extends('layouts.app')

@section('title', 'New Sale')

@section('content')

<div class="container-fluid">

    {{-- =========================
         HEADER
    ========================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>New Sale</h2>

        {{-- BACK TO INVOICES --}}
        <a href="{{ route('invoices.index') }}"
           class="btn btn-secondary">
            ← Back
        </a>

    </div>


    {{-- =========================
         ERROR
    ========================== --}}
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Please check the following:</strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================
         SALE FORM
    ========================== --}}
    <form action="{{ route('sales.store') }}"
          method="POST"
          id="saleForm">

        @csrf


        {{-- =========================
             SALE INFORMATION
        ========================== --}}
        <div class="card mb-4">

            <div class="card-header bg-dark text-white">

                <strong>
                    Sale Information
                </strong>

            </div>


            <div class="card-body">

                <div class="row">


                    {{-- Invoice --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Invoice No
                        </label>

                        <input type="text"
                               name="invoice_no"
                               class="form-control"
                               value="{{ old('invoice_no', 'INV-' . date('YmdHis')) }}"
                               readonly>

                        @error('invoice_no')

                            <div class="text-danger">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Customer --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Customer
                        </label>

                        <select name="customer_id"
                                class="form-select">

                            <option value="">
                                Walk-in Customer
                            </option>

                            @foreach($customers as $customer)

                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>

                                    {{ $customer->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Payment Method --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Payment Method
                        </label>

                        <select name="payment_method"
                                class="form-select"
                                required>

                            <option value="cash">
                                Cash
                            </option>

                            <option value="card">
                                Card
                            </option>

                            <option value="bank">
                                Bank
                            </option>

                            <option value="other">
                                Other
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================
             ADD PRODUCT
        ========================== --}}
        <div class="card mb-4">

            <div class="card-header bg-primary text-white">

                <strong>
                    Add Product
                </strong>

            </div>


            <div class="card-body">

                <div class="row align-items-end">


                    {{-- Product --}}
                    <div class="col-md-5 mb-3">

                        <label class="form-label">
                            Product
                        </label>

                        <select id="productSelect"
                                class="form-select">

                            <option value="">
                                -- Select Product --
                            </option>

                            @foreach($products as $product)

                                <option value="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->quantity }}">

                                    {{ $product->name }}
                                    -
                                    ${{ number_format($product->price, 2) }}
                                    |
                                    Stock: {{ $product->quantity }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Quantity --}}
                    <div class="col-md-2 mb-3">

                        <label class="form-label">
                            Quantity
                        </label>

                        <input type="number"
                               id="productQuantity"
                               class="form-control"
                               value="1"
                               min="1">

                    </div>


                    {{-- Price --}}
                    <div class="col-md-2 mb-3">

                        <label class="form-label">
                            Price
                        </label>

                        <input type="number"
                               id="productPrice"
                               class="form-control"
                               step="0.01"
                               min="0">

                    </div>


                    {{-- Add --}}
                    <div class="col-md-3 mb-3">

                        <button type="button"
                                id="addProduct"
                                class="btn btn-success w-100">

                            + Add Product

                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================
             SALE ITEMS
        ========================== --}}
        <div class="card mb-4">

            <div class="card-header bg-secondary text-white">

                <strong>
                    Sale Items
                </strong>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered mb-0">

                        <thead class="table-dark">

                            <tr>

                                <th width="5%">
                                    #
                                </th>

                                <th>
                                    Product
                                </th>

                                <th width="15%">
                                    Price
                                </th>

                                <th width="12%">
                                    Quantity
                                </th>

                                <th width="15%">
                                    Total
                                </th>

                                <th width="10%">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody id="saleItems">

                            <tr id="emptyRow">

                                <td colspan="6"
                                    class="text-center text-muted py-4">

                                    No products added.

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- =========================
             PAYMENT SUMMARY
        ========================== --}}
        <div class="row justify-content-end">

            <div class="col-md-5">

                <div class="card">

                    <div class="card-header bg-dark text-white">

                        <strong>
                            Payment Summary
                        </strong>

                    </div>


                    <div class="card-body">


                        {{-- Subtotal --}}
                        <div class="d-flex justify-content-between mb-3">

                            <span>
                                Subtotal
                            </span>

                            <strong>

                                $
                                <span id="subtotalDisplay">
                                    0.00
                                </span>

                            </strong>

                        </div>


                        {{-- Discount --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Discount
                            </label>

                            <input type="number"
                                   name="discount"
                                   id="discount"
                                   class="form-control"
                                   value="0"
                                   min="0"
                                   step="0.01">

                        </div>


                        {{-- Tax --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Tax
                            </label>

                            <input type="number"
                                   name="tax"
                                   id="tax"
                                   class="form-control"
                                   value="0"
                                   min="0"
                                   step="0.01">

                        </div>


                        {{-- Total --}}
                        <div class="d-flex justify-content-between border-top pt-3 mb-3">

                            <h5>
                                Total
                            </h5>

                            <h5>

                                $
                                <span id="totalDisplay">
                                    0.00
                                </span>

                            </h5>

                        </div>


                        {{-- Paid --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Paid
                            </label>

                            <input type="number"
                                   name="paid"
                                   id="paid"
                                   class="form-control"
                                   value="0"
                                   min="0"
                                   step="0.01">

                        </div>


                        {{-- Change --}}
                        <div class="d-flex justify-content-between">

                            <span>
                                Change
                            </span>

                            <strong class="text-success">

                                $
                                <span id="changeDisplay">
                                    0.00
                                </span>

                            </strong>

                        </div>


                        {{-- Due --}}
                        <div class="d-flex justify-content-between mt-2">

                            <span>
                                Due
                            </span>

                            <strong class="text-danger">

                                $
                                <span id="dueDisplay">
                                    0.00
                                </span>

                            </strong>

                        </div>


                        {{-- Note --}}
                        <div class="mt-4">

                            <label class="form-label">
                                Note
                            </label>

                            <textarea name="note"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Optional note">{{ old('note') }}</textarea>

                        </div>


                        {{-- SAVE --}}
                        <div class="mt-4">

                            <button type="submit"
                                    class="btn btn-success btn-lg w-100">

                                💰 Save Sale

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- =========================
     JAVASCRIPT
========================== --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const productSelect =
        document.getElementById('productSelect');

    const quantityInput =
        document.getElementById('productQuantity');

    const priceInput =
        document.getElementById('productPrice');

    const addButton =
        document.getElementById('addProduct');

    const saleItems =
        document.getElementById('saleItems');

    const discountInput =
        document.getElementById('discount');

    const taxInput =
        document.getElementById('tax');

    const paidInput =
        document.getElementById('paid');

    const subtotalDisplay =
        document.getElementById('subtotalDisplay');

    const totalDisplay =
        document.getElementById('totalDisplay');

    const changeDisplay =
        document.getElementById('changeDisplay');

    const dueDisplay =
        document.getElementById('dueDisplay');


    let itemIndex = 0;


    // =========================
    // Product Select
    // =========================

    productSelect.addEventListener('change', function () {

        const option =
            this.options[this.selectedIndex];

        if (!option.value) {

            priceInput.value = '';

            return;
        }

        priceInput.value =
            parseFloat(option.dataset.price).toFixed(2);

    });


    // =========================
    // Add Product
    // =========================

    addButton.addEventListener('click', function () {

        const option =
            productSelect.options[
                productSelect.selectedIndex
            ];


        if (!option.value) {

            alert('Please select a product.');

            return;
        }


        const productId =
            option.value;

        const productName =
            option.dataset.name;

        const stock =
            parseInt(option.dataset.stock);

        const quantity =
            parseInt(quantityInput.value);

        const price =
            parseFloat(priceInput.value);


        if (!quantity || quantity < 1) {

            alert('Quantity must be at least 1.');

            return;
        }


        if (quantity > stock) {

            alert(
                'Not enough stock. Available: ' + stock
            );

            return;
        }


        if (isNaN(price) || price < 0) {

            alert('Invalid price.');

            return;
        }


        // Remove empty row

        const emptyRow =
            document.getElementById('emptyRow');

        if (emptyRow) {

            emptyRow.remove();

        }


        // =========================
        // Check Duplicate Product
        // =========================

        const existing =
            document.querySelectorAll(
                '#saleItems tr[data-product-id]'
            );


        for (let row of existing) {

            if (
                row.dataset.productId == productId
            ) {

                const qtyInput =
                    row.querySelector('.item-quantity');

                let newQuantity =
                    parseInt(qtyInput.value) + quantity;


                if (newQuantity > stock) {

                    alert(
                        'Not enough stock. Available: ' + stock
                    );

                    return;
                }


                qtyInput.value =
                    newQuantity;

                updateRowTotal(row);

                calculateTotals();

                return;
            }

        }


        // =========================
        // Create Row
        // =========================

        const row =
            document.createElement('tr');

        row.dataset.productId =
            productId;


        const total =
            quantity * price;


        row.innerHTML = `

            <td class="row-number">
                ${itemIndex + 1}
            </td>

            <td>

                ${productName}

                <input
                    type="hidden"
                    name="items[${itemIndex}][product_id]"
                    value="${productId}">
            </td>

            <td>

                $${price.toFixed(2)}

                <input
                    type="hidden"
                    name="items[${itemIndex}][price]"
                    value="${price}">

            </td>

            <td>

                <input
                    type="number"
                    name="items[${itemIndex}][quantity]"
                    value="${quantity}"
                    min="1"
                    max="${stock}"
                    class="form-control item-quantity">

            </td>

            <td>

                <strong class="item-total">

                    $${total.toFixed(2)}

                </strong>

            </td>

            <td>

                <button
                    type="button"
                    class="btn btn-danger btn-sm remove-item">

                    Remove

                </button>

            </td>

        `;


        saleItems.appendChild(row);


        // =========================
        // Quantity Change
        // =========================

        const qtyInput =
            row.querySelector('.item-quantity');


        qtyInput.addEventListener(
            'input',
            function () {

                const max =
                    parseInt(this.max);

                let value =
                    parseInt(this.value);


                if (isNaN(value) || value < 1) {

                    value = 1;

                }


                if (value > max) {

                    alert(
                        'Maximum stock: ' + max
                    );

                    value = max;

                }


                this.value =
                    value;


                updateRowTotal(row);

                calculateTotals();

            }
        );


        // =========================
        // Remove
        // =========================

        row.querySelector('.remove-item')
            .addEventListener(
                'click',
                function () {

                    row.remove();

                    updateRowNumbers();

                    calculateTotals();


                    if (
                        saleItems.querySelectorAll(
                            'tr[data-product-id]'
                        ).length === 0
                    ) {

                        saleItems.innerHTML = `

                            <tr id="emptyRow">

                                <td colspan="6"
                                    class="text-center text-muted py-4">

                                    No products added.

                                </td>

                            </tr>

                        `;

                    }

                }
            );


        itemIndex++;

        calculateTotals();


        // Reset product fields

        productSelect.value = '';

        quantityInput.value = 1;

        priceInput.value = '';

    });


    // =========================
    // Update Row Total
    // =========================

    function updateRowTotal(row) {

        const quantity =
            parseInt(
                row.querySelector(
                    '.item-quantity'
                ).value
            ) || 0;


        const price =
            parseFloat(
                row.querySelector(
                    'input[name$="[price]"]'
                ).value
            ) || 0;


        const total =
            quantity * price;


        row.querySelector(
            '.item-total'
        ).textContent =
            '$' + total.toFixed(2);

    }


    // =========================
    // Update Row Numbers
    // =========================

    function updateRowNumbers() {

        const rows =
            saleItems.querySelectorAll(
                'tr[data-product-id]'
            );


        rows.forEach(
            function (row, index) {

                row.querySelector(
                    '.row-number'
                ).textContent =
                    index + 1;

            }
        );

    }


    // =========================
    // Calculate Totals
    // =========================

    function calculateTotals() {

        let subtotal = 0;


        const rows =
            saleItems.querySelectorAll(
                'tr[data-product-id]'
            );


        rows.forEach(
            function (row) {

                const quantity =
                    parseInt(
                        row.querySelector(
                            '.item-quantity'
                        ).value
                    ) || 0;


                const price =
                    parseFloat(
                        row.querySelector(
                            'input[name$="[price]"]'
                        ).value
                    ) || 0;


                subtotal +=
                    quantity * price;

            }
        );


        let discount =
            parseFloat(
                discountInput.value
            ) || 0;


        let tax =
            parseFloat(
                taxInput.value
            ) || 0;


        if (discount > subtotal) {

            discount = subtotal;

            discountInput.value =
                discount.toFixed(2);

        }


        let total =
            subtotal - discount + tax;


        if (total < 0) {

            total = 0;

        }


        let paid =
            parseFloat(
                paidInput.value
            ) || 0;


        let change = 0;

        let due = 0;


        if (paid > total) {

            change =
                paid - total;

        } else {

            due =
                total - paid;

        }


        subtotalDisplay.textContent =
            subtotal.toFixed(2);

        totalDisplay.textContent =
            total.toFixed(2);

        changeDisplay.textContent =
            change.toFixed(2);

        dueDisplay.textContent =
            due.toFixed(2);

    }


    // =========================
    // Recalculate
    // =========================

    discountInput.addEventListener(
        'input',
        calculateTotals
    );


    taxInput.addEventListener(
        'input',
        calculateTotals
    );


    paidInput.addEventListener(
        'input',
        calculateTotals
    );


    // =========================
    // Prevent Empty Sale
    // =========================

    document.getElementById('saleForm')
        .addEventListener(
            'submit',
            function (event) {

                const rows =
                    saleItems.querySelectorAll(
                        'tr[data-product-id]'
                    );


                if (rows.length === 0) {

                    event.preventDefault();

                    alert(
                        'Please add at least one product.'
                    );

                    return;

                }

            }
        );


    // Initial calculation

    calculateTotals();

});

</script>

@endsection