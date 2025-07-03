@extends('admin.layouts.app')
@section('title', 'Add Category')

@section('admin-content')
<div class="right_col" role="main">
    <div class="container">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add New Category</h2>
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

                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name">Category Name:</label>
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                    </div>

                    <div class="form-group">
                        <label for="parent_id">Parent Category (optional):</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">None</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Add Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
