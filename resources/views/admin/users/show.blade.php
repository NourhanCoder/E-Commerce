@extends('admin.layouts.app')

@section('title', 'User Details')
@section('admin-content')

    <div class="right_col" role="main">
        <div class="container">
            <h2>User Details</h2>

            <div class="card p-4">
                <p><strong>Image:</strong>
                    @if ($user->image)
                        <img src="{{ $user->image() }}" alt="User Image" width="150">
                    @else
                        <p>No image uploaded</p>
                    @endif
                </p>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Type:</strong> {{ $user->type }}</p>
                <p><strong>Status:</strong>
                    @if ($user->active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </p>
            </div>

            <a href="{{ route('admin.panel') }}" class="btn btn-secondary mt-3">Back</a>
        </div>
    </div>

@endsection
