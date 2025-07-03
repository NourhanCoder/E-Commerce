@extends('admin.layouts.app')

@section('title', 'Add User')
@section('admin-content')

    <div class="right_col" role="main">
        <div class="container">
            <h2>Add New User</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="image">Profile Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label>Type:</label>
                    <select name="type" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password:</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="active" class="form-check-input" id="active">
                    <label class="form-check-label" for="active">Active</label>
                </div>

                <button type="submit" class="btn btn-success">Add User</button>
                <a href="{{ route('admin.panel') }}" class="btn btn-primary">Cancel</a>

            </form>
        </div>
    </div>

@endsection
