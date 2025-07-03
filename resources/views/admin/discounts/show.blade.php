@extends('admin.layouts.app')

@section('title', 'Discount Details')

@section('admin-content')
<div class="right_col" role="main">
    <div class="container mt-5">
        <div class="x_panel">
            <div class="x_title">
                <h2>Discount Details</h2>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Book:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $discount->product->title }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Image:</strong>
                    </div>
                    <div class="col-md-9">
                        <img src="{{ $discount->product->image() }}" width="150" alt="Book image">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Discount Percentage:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $discount->percentage }}%
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Start Date:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $discount->start_date }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>End Date:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $discount->end_date }}
                    </div>
                </div>

                <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
