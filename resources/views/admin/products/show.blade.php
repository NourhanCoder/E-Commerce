@extends('admin.layouts.app')
@section('title', 'Show Product')

@section('admin-content')
    <div class="right_col" role="main">
        <div class="container">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Product Details</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <div class="mb-3">
                        <strong>Image:</strong><br>
                        @if ($product->image)
                            <img src="{{ $product->image() }}" width="200">
                        @else
                            <p>No image available.</p>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Title:</strong> {{ $product->title }}
                    </div>

                    <div class="mb-3">
                        <strong>Author:</strong> {{ $product->author }}
                    </div>

                    <div class="mb-3">
                        <strong>Description:</strong> {{ $product->description }}
                    </div>

                    <div class="mb-3">
                        <strong>Price:</strong> {{ number_format($product->price, 2) }}EGP
                    </div>

                    <div class="mb-3">
                        <strong>Stock:</strong> {{ $product->stock }}
                    </div>

                    <div class="mb-3">
                        <strong>SKU:</strong> {{ $product->sku }}
                    </div>

                    <p><strong>Category:</strong>
                        @if ($product->category)
                            {{ $product->category->name }}
                            @if ($product->category->parent)
                                (Parent: {{ $product->category->parent->name }})
                            @endif
                        @else
                            No category assigned
                        @endif
                    </p>

                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection
