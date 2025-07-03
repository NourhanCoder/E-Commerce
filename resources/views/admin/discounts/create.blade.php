@extends('admin.layouts.app')

@section('title', 'Add Offer')

@section('admin-content')
<div class="right_col" role="main">
    <div class="container">
        <div class="x_panel">
            <div class="x_title d-flex justify-content-between align-items-center">
                <h2>Add Offer</h2>
            </div>
            <div class="x_content">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.discounts.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="product_id">Select Book</label>
                        <select name="product_id" id="product_id" class="form-control" required>
                            <option value="">-- Choose Book --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="percentage">Discount Percentage (%)</label>
                        <input type="number" name="percentage" id="percentage" class="form-control" min="1" max="100" required>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Start Date & Time</label>
                        <input type="datetime-local" name="start_date" id="start_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date & Time</label>
                        <input type="datetime-local" name="end_date" id="end_date" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success">Add Offer</button>
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-primary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
