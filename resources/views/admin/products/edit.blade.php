@extends('admin.layouts.app')
@section('title', 'Edit Product')

@section('admin-content')
<div class="right_col" role="main">
    <div class="container">
        <div class="x_panel">
            <div class="x_title">
                <h2>Edit Product</h2>
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

                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Current Image --}}
                    @if ($product->image)
                        <div class="mb-3">
                            <label>Current Image:</label><br>
                            <img src="{{ $product->image() }}" width="150">
                        </div>
                    @endif

                    {{-- Title --}}
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $product->title) }}" required>
                    </div>

                    {{-- Author --}}
                    <div class="form-group">
                        <label>Author:</label>
                        <input type="text" name="author" class="form-control" value="{{ old('author', $product->author) }}" required>
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div class="form-group">
                        <label>Price:</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                    </div>

                    {{-- Stock --}}
                    <div class="form-group">
                        <label>Stock:</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                    </div>

                    {{-- SKU --}}
                    <div class="form-group">
                        <label>SKU:</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>

                    {{-- Category --}}
                    <div class="form-group">
                        <label>Category:</label>
                        <select name="category_id" class="form-control">
                            @foreach ($categories as $categ)
                                <option value="{{ $categ->id }}" {{ $product->category_id == $categ->id ? 'selected' : '' }}>
                                    {{ $categ->parent ? $categ->parent->name . ' ← ' . $categ->name : $categ->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Image --}}
                    <div class="form-group">
                        <label>Upload New Image:</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    {{-- Buttons --}}
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
