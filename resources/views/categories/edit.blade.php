@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')

<h2>Edit Category</h2>

<form action="{{ route('categories.update', $category->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="mb-3">

        <label class="form-label">
            Category Name
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name', $category->name) }}">

        @error('name')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="mb-3">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control">{{ old('description', $category->description) }}</textarea>

    </div>


    <button class="btn btn-success">
        Update
    </button>

    <a href="{{ route('categories.index') }}"
       class="btn btn-secondary">
        Back
    </a>

</form>

@endsection