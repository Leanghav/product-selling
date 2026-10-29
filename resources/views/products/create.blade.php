@extends('layouts.app')

@section('title', 'Add Product')

@section('content')

<h2>Add Product</h2>

<form action="{{ route('products.store') }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf


    <div class="mb-3">

        <label class="form-label">
            Category
        </label>

        <select name="category_id"
                class="form-select">

            <option value="">
                -- Select Category --
            </option>

            @foreach($categories as $category)

                <option value="{{ $category->id }}">

                    {{ $category->name }}

                </option>

            @endforeach

        </select>

        @error('category_id')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="mb-3">

        <label class="form-label">
            Product Name
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name') }}">

        @error('name')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror

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
                       value="{{ old('price') }}">

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
                       value="{{ old('quantity', 0) }}">

            </div>

        </div>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control"
                  rows="4">{{ old('description') }}</textarea>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Product Image
        </label>

        <input type="file"
               name="image"
               class="form-control">

    </div>


    <div class="form-check mb-3">

        <input type="checkbox"
               name="status"
               class="form-check-input"
               checked>

        <label class="form-check-label">
            Active
        </label>

    </div>


    <button class="btn btn-success">
        Save Product
    </button>

    <a href="{{ route('products.index') }}"
       class="btn btn-secondary">
        Back
    </a>

</form>

@endsection