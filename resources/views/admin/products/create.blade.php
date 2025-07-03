@extends('admin.layouts.app')
@section('title', 'Add Product')

@section('admin-content')
<div class="right_col" role="main">
    <div class="container">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add New Product</h2>
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

                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="title">Product Title:</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="author">Author:</label>
                        <input type="text" name="author" id="author" class="form-control" value="{{ old('author') }}">
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" required step="0.01">
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="sku">SKU:</label>
                        <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku') }}">
                    </div>

                    <div class="form-group">
                        <label for="category_id">Select Subcategory:</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">
                                    {{ $subcategory->parent->name }} - {{ $subcategory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="image">Product Image:</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-success">Add Product</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Cancel</a>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
