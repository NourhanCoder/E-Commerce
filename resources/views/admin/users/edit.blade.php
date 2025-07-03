@extends('admin.layouts.app')
@section('title', 'Edit User')

@section('admin-content')
    <div class="right_col" role="main">
        <div class="container">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Edit User</h2>
                    <div class="clearfix"></div>

                </div>

                <div class="x_content">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($user->image())
                            <div class="mb-3">
                                <label>Current Image:</label><br>
                                <img src="{{ $user->image() }}" alt="User Image" width="150">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="image" class="form-label">Upload New Image</label>
                            <input type="file" name="image" id="image" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="type">Type:</label>
                            <select name="type" class="form-control" required>
                                <option value="admin" {{ $user->type == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="customer" {{ $user->type == 'customer' ? 'selected' : '' }}>Customer</option>
                            </select>
                        </div>

                        <div class="form-check mb-3 mt-2">
                            <input class="form-check-input" type="checkbox" name="active" id="active"
                                {{ $user->active ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.panel') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                @endsection
