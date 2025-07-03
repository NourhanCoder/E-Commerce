@extends('admin.layouts.app')
@section('title', 'All Products')

@section('admin-content')
<div class="right_col" role="main">
    <div class="container">
        <div class="x_panel">
            <div class="x_title d-flex justify-content-between align-items-center">
                <h2>All Products</h2>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>SKU</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $index => $product)
                            <tr>
                                <td>{{ $products->firstItem() + $index  }}</td>
                                <td>{{ $product->title }}</td>
                                <td>{{ $product->author }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->price }} EGP</td>
                                <td>{{ $product->stock }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>
                                    
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info btn-sm">View</a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
