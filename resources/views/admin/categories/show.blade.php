@extends('admin.layouts.app')
@section('title', 'Show Category')

@section('admin-content')
    <div class="right_col" role="main">
        <div class="container">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Category Details</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="mb-3">
                        <strong>Name:</strong> {{ $category->name }}
                    </div>

                    <div class="mb-3">
                        <strong>Parent Category:</strong>
                        {{ $category->parent ? $category->parent->name : '-' }}
                    </div>

                    <div class="mb-3">
                        <strong>Products:</strong>
                        @if ($category->products->count())
                            <ul>
                                @foreach ($category->products as $product)
                                    <li>{{ $product->title }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>No products found for this category.</p>
                        @endif
                    </div>


                    <div class="mb-3">
                        <strong>Created At:</strong> {{ $category->created_at->format('Y-m-d H:i') }}
                    </div>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection
