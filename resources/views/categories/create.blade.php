@extends('layouts.app')

@section('title', 'Add Category')

@section('content')

<h2>Add Category</h2>

<form action="{{ route('categories.store') }}"
      method="POST">

    @csrf

    <div class="mb-3">

        <label class="form-label">
            Category Name
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


    <div class="mb-3">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control">{{ old('description') }}</textarea>

    </div>


    <button class="btn btn-success">
        Save
    </button>

    <a href="{{ route('categories.index') }}"
       class="btn btn-secondary">
        Back
    </a>

</form>

@endsection