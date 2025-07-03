@extends('admin.layouts.app')
@section('title', 'Edit Category')

@section('admin-content')
<div class="right_col" role="main">
    <div class="container">
        <div class="x_panel">
            <div class="x_title">
                <h2>Edit Category</h2>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Category Name:</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="parent_id">Parent Category (optional):</label>
                        <select name="parent_id" class="form-control">
                            <option value="">None</option>
                            @foreach ($categories as $categ)
                                <option value="{{ $categ->id }}" {{ $category->parent_id == $categ->id ? 'selected' : '' }}>
                                    {{ $categ->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
