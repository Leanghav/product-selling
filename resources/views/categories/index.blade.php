@extends('layouts.app')

@section('title', 'Categories')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h2>Categories</h2>

    <a href="{{ route('categories.create') }}"
       class="btn btn-primary">
        + Add Category
    </a>

</div>


<table class="table table-bordered table-hover">

    <thead class="table-dark">

        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th width="180">Action</th>
        </tr>

    </thead>

    <tbody>

        @foreach($categories as $category)

        <tr>

            <td>{{ $category->id }}</td>

            <td>{{ $category->name }}</td>

            <td>{{ $category->description }}</td>

            <td>

                <a href="{{ route('categories.edit', $category->id) }}"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('categories.destroy', $category->id) }}"
                      method="POST"
                      class="d-inline">

                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Delete this category?')">
                        Delete
                    </button>

                </form>

            </td>

        </tr>

        @endforeach

    </tbody>

</table>

@endsection