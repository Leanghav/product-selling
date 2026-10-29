@extends('layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Invoice</h2>

        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
            ← Back
        </a>
    </div>

    {{-- Error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sales.update', $sale->id) }}" method="POST">

        @csrf
        @method('PUT')

        {{-- Invoice Information --}}
        <div class="card mb-4">
            <div class="card-header">
                <strong>Invoice Information</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- Invoice No --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Invoice No
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $sale->invoice_no }}"
                            readonly
                        >
                    </div>

                    {{-- Customer --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Customer
                        </label>

                        <select
                            name="customer_id"
                            class="form-control"
                        >

                            <option value="">
                                Walk-in Customer
                            </option>

                            @foreach ($customers as $customer)

                                <option
                                    value="{{ $customer->id }}"
                                    {{ $sale->customer_id == $customer->id ? 'selected' : '' }}
                                >
                                    {{ $customer->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Payment --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Payment Method
                        </label>

                        <select
                            name="payment_method"
                            class="form-control"
                        >

                            <option value="cash"
                                {{ $sale->payment_method == 'cash' ? 'selected' : '' }}>
                                Cash
                            </option>

                            <option value="card"
                                {{ $sale->payment_method == 'card' ? 'selected' : '' }}>
                                Card
                            </option>

                            <option value="bank"
                                {{ $sale->payment_method == 'bank' ? 'selected' : '' }}>
                                Bank
                            </option>

                            <option value="other"
                                {{ $sale->payment_method == 'other' ? 'selected' : '' }}>
                                Other
                            </option>

                        </select>

                    </div>

                </div>

            </div>
        </div>


        {{-- Products --}}
        <div class="card mb-4">

            <div class="card-header">
                <strong>Products</strong>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead>

                            <tr>
                                <th width="35%">
                                    Product
                                </th>

                                <th width="15%">
                                    Quantity
                                </th>

                                <th width="20%">
                                    Price
                                </th>

                                <th width="20%">
                                    Total
                                </th>

                                <th width="10%">
                                    Action
                                </th>
                            </tr>

                        </thead>

                        <tbody id="items">

                            @foreach ($sale->items as $index => $item)

                                <tr class="item-row">

                                    <td>

                                        <select
                                            name="items[{{ $index }}][product_id]"
                                            class="form-control product"
                                            required
                                        >

                                            <option value="">
                                                Select Product
                                            </option>

                                            @foreach ($products as $product)

                                                <option
                                                    value="{{ $product->id }}"
                                                    data-price="{{ $product->price }}"
                                                    {{ $item->product_id == $product->id ? 'selected' : '' }}
                                                >
                                                    {{ $product->name }}
                                                    (Stock: {{ $product->quantity }})
                                                </option>

                                            @endforeach

                                        </select>

                                    </td>


                                    <td>

                                        <input
                                            type="number"
                                            name="items[{{ $index }}][quantity]"
                                            class="form-control quantity"
                                            value="{{ $item->quantity }}"
                                            min="1"
                                            required
                                        >

                                    </td>


                                    <td>

                                        <input
                                            type="number"
                                            name="items[{{ $index }}][price]"
                                            class="form-control price"
                                            value="{{ $item->price }}"
                                            min="0"
                                            step="0.01"
                                            required
                                        >

                                    </td>


                                    <td>

                                        <input
                                            type="text"
                                            class="form-control total"
                                            value="{{ number_format($item->total, 2) }}"
                                            readonly
                                        >

                                    </td>


                                    <td>

                                        <button
                                            type="button"
                                            class="btn btn-danger remove-item"
                                        >
                                            Remove
                                        </button>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                <button
                    type="button"
                    id="add-item"
                    class="btn btn-primary"
                >
                    + Add Product
                </button>

            </div>

        </div>


        {{-- Summary --}}
        <div class="card mb-4">

            <div class="card-header">
                <strong>Summary</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 offset-md-8">

                        <div class="mb-3">

                            <label class="form-label">
                                Subtotal
                            </label>

                            <input
                                type="number"
                                name="subtotal"
                                id="subtotal"
                                class="form-control"
                                value="{{ $sale->subtotal }}"
                                readonly
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Discount
                            </label>

                            <input
                                type="number"
                                name="discount"
                                id="discount"
                                class="form-control"
                                value="{{ $sale->discount }}"
                                min="0"
                                step="0.01"
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Tax
                            </label>

                            <input
                                type="number"
                                name="tax"
                                id="tax"
                                class="form-control"
                                value="{{ $sale->tax }}"
                                min="0"
                                step="0.01"
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Total
                            </label>

                            <input
                                type="number"
                                name="total"
                                id="total"
                                class="form-control"
                                value="{{ $sale->total }}"
                                readonly
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Paid
                            </label>

                            <input
                                type="number"
                                name="paid"
                                id="paid"
                                class="form-control"
                                value="{{ $sale->paid }}"
                                min="0"
                                step="0.01"
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Change
                            </label>

                            <input
                                type="number"
                                id="change"
                                class="form-control"
                                value="{{ $sale->change }}"
                                readonly
                            >

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Note --}}
        <div class="card mb-4">

            <div class="card-body">

                <label class="form-label">
                    Note
                </label>

                <textarea
                    name="note"
                    class="form-control"
                    rows="3"
                >{{ $sale->note }}</textarea>

            </div>

        </div>


        {{-- Submit --}}
        <div class="text-end">

            <a
                href="{{ route('invoices.index') }}"
                class="btn btn-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-success"
            >
                Update Invoice
            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const items = document.getElementById('items');

    const addButton = document.getElementById('add-item');

    let index = {{ $sale->items->count() }};


    function calculate() {

        let subtotal = 0;

        document.querySelectorAll('.item-row').forEach(function(row) {

            const quantity =
                parseFloat(row.querySelector('.quantity').value) || 0;

            const price =
                parseFloat(row.querySelector('.price').value) || 0;

            const total = quantity * price;

            row.querySelector('.total').value =
                total.toFixed(2);

            subtotal += total;

        });


        const discount =
            parseFloat(document.getElementById('discount').value) || 0;

        const tax =
            parseFloat(document.getElementById('tax').value) || 0;

        const finalDiscount =
            Math.min(discount, subtotal);

        const total =
            (subtotal - finalDiscount) + tax;

        const paid =
            parseFloat(document.getElementById('paid').value) || 0;

        const change =
            Math.max(0, paid - total);


        document.getElementById('subtotal').value =
            subtotal.toFixed(2);

        document.getElementById('total').value =
            total.toFixed(2);

        document.getElementById('change').value =
            change.toFixed(2);

    }


    addButton.addEventListener('click', function () {

        const row = document.createElement('tr');

        row.classList.add('item-row');

        row.innerHTML = `

            <td>

                <select
                    name="items[${index}][product_id]"
                    class="form-control product"
                    required
                >

                    <option value="">
                        Select Product
                    </option>

                    @foreach ($products as $product)

                        <option
                            value="{{ $product->id }}"
                            data-price="{{ $product->price }}"
                        >
                            {{ $product->name }}
                            (Stock: {{ $product->quantity }})
                        </option>

                    @endforeach

                </select>

            </td>


            <td>

                <input
                    type="number"
                    name="items[${index}][quantity]"
                    class="form-control quantity"
                    value="1"
                    min="1"
                    required
                >

            </td>


            <td>

                <input
                    type="number"
                    name="items[${index}][price]"
                    class="form-control price"
                    value="0"
                    min="0"
                    step="0.01"
                    required
                >

            </td>


            <td>

                <input
                    type="text"
                    class="form-control total"
                    value="0.00"
                    readonly
                >

            </td>


            <td>

                <button
                    type="button"
                    class="btn btn-danger remove-item"
                >
                    Remove
                </button>

            </td>

        `;

        items.appendChild(row);

        index++;

    });


    items.addEventListener('change', function(e) {

        if (e.target.classList.contains('product')) {

            const option =
                e.target.options[e.target.selectedIndex];

            const price =
                option.dataset.price || 0;

            e.target
                .closest('.item-row')
                .querySelector('.price')
                .value = price;

            calculate();
        }

    });


    items.addEventListener('input', function(e) {

        if (
            e.target.classList.contains('quantity') ||
            e.target.classList.contains('price')
        ) {

            calculate();

        }

    });


    items.addEventListener('click', function(e) {

        if (e.target.classList.contains('remove-item')) {

            const rows =
                document.querySelectorAll('.item-row');

            if (rows.length > 1) {

                e.target.closest('.item-row').remove();

                calculate();

            }

        }

    });


    document.getElementById('discount')
        .addEventListener('input', calculate);

    document.getElementById('tax')
        .addEventListener('input', calculate);

    document.getElementById('paid')
        .addEventListener('input', calculate);


    calculate();

});

</script>

@endsection