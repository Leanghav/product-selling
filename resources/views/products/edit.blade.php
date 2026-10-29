@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')

<h2>Edit Product</h2>

<form action="{{ route('products.update', $product->id) }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf
    @method('PUT')


    <div class="mb-3">

        <label class="form-label">
            Category
        </label>

        <select name="category_id"
                class="form-select">

            @foreach($categories as $category)

                <option value="{{ $category->id }}"
                    {{ $product->category_id == $category->id ? 'selected' : '' }}>

                    {{ $category->name }}

                </option>

            @endforeach

        </select>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Product Name
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name', $product->name) }}">

    </div>


    <div class="row">

        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Price
                </label>

                <input type="number"
                       step="0.01"
                       name="price"
                       class="form-control"
                       value="{{ old('price', $product->price) }}">

            </div>

        </div>


        <div class="col-md-6">

            <div class="mb-3">

                <label class="form-label">
                    Quantity
                </label>

                <input type="number"
                       name="quantity"
                       class="form-control"
                       value="{{ old('quantity', $product->quantity) }}">

            </div>

        </div>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control"
                  rows="4">{{ old('description', $product->description) }}</textarea>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Current Image
        </label>

        <br>

        @if($product->image)

            <img src="{{ asset('storage/' . $product->image) }}"
                 width="120"
                 height="120"
                 style="object-fit:cover"
                 class="mb-2">

        @else

            <p>No Image</p>

        @endif

    </div>


    <div class="mb-3">

        <label class="form-label">
            Change Image
        </label>

        <input type="file"
               name="image"
               class="form-control">

    </div>


    <div class="form-check mb-3">

        <input type="checkbox"
               name="status"
               class="form-check-input"
               {{ $product->status ? 'checked' : '' }}>

        <label class="form-check-label">
            Active
        </label>

    </div>


    <button class="btn btn-success">
        Update Product
    </button>

    <a href="{{ route('products.index') }}"
       class="btn btn-secondary">
        Back
    </a>

</form>

@endsection