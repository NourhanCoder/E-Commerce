@extends('admin.layouts.app')

@section('title', 'All Offers')

@section('admin-content')
<div class="right_col" role="main">
    <div class="container">
        <div class="x_panel">
            <div class="x_title d-flex justify-content-between align-items-center">
                <h2>All Offers</h2>
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
                            <th>Book</th>
                            <th>Image</th>
                            <th>Discount (%)</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($discounts as $index => $discount)
                            <tr>
                                <td>{{ $discounts->firstItem() + $index }}</td>
                                <td>{{ $discount->product->title }}</td>
                                <td><img src="{{ $discount->product->image() }}" width="80" alt="Book Image"></td>
                                <td>{{ $discount->percentage }}%</td>
                                <td>{{ $discount->start_date }}</td>
                                <td>{{ $discount->end_date }}</td>
                                <td>
                                    <a href="{{ route('admin.discounts.show', $discount->id) }}" class="btn btn-info btn-sm">View</a>
                                    <a href="{{ route('admin.discounts.edit', $discount->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.discounts.destroy', $discount->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure that you want to delete this discount?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted text-center">No offers available at the moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $discounts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
