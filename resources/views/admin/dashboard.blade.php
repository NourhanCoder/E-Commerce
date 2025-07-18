@extends('admin.layouts.app')

@section('title', 'Users List')
@section('admin-content')


<div class="right_col" role="main">
  <div class="container">
    @if($lowStockProducts->count() > 0)
  <div class="card border-warning mb-4 shadow-sm">
    <div class="card-header bg-warning d-flex align-items-center">
      <i class="fa-solid fa-triangle-exclamation text-danger me-2 fs-5"></i>
      <strong class="text-dark">
        ⚠️ Attention: Low Stock Products
      </strong>
    </div>
    <div class="card-body bg-light">
      <p class="mb-2">The following products have low stock levels. Please take action:</p>
      <ul class="list-group list-group-flush">
        @foreach($lowStockProducts as $product)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong>{{ $product->title }}</strong>
              — only <span class="text-danger fw-bold">{{ $product->stock }}</span> left in stock.
            </div>
            <div>
              <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info me-2">
                View
              </a>
              <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                Edit Stock
              </a>
            </div>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
@endif

    <h2>Users List</h2> 

    @if (session('success'))
  <div class="alert alert-success mt-3">
    {{ session('success') }}
  </div>
@endif
    <table class="table table-bordered table-striped text-center">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Type</th>
          <th>Status</th>
          <th>Show</th>
          <th>Edit</th>
          <th>Delete</th>
          <th>Activate / Deactivate</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $index => $user)
        <tr>
          <td>{{ $users->firstItem() + $index }}</td> 
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->type }}</td>
          <td>
            @if($user->active)
              <span class="badge bg-success">Active</span>
            @else
              <span class="badge bg-danger">Inactive</span>
            @endif
          </td>
          <td>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm">View</a>
          </td>

          <td>
            <a href="{{ route('admin.users.edit', $user->id) }}">
              <img src="{{ asset('admin/images/edit.png') }}" alt="Edit">
            </a>
          </td>
          <td>
            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
              class="d-inline" onsubmit="return confirm('Are you sure that you want to delete this item?')">
              @csrf
              @method('DELETE')
              <button type="submit" style="background: none; border: none;">
                <img src="{{ asset('admin/images/delete.png') }}" alt="Delete">
              </button>
            </form>
          </td>
          <td>
            <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
              @csrf
              <button type="submit" class="btn btn-sm {{ $user->active ? 'btn-danger' : 'btn-success' }}">
                {{ $user->active ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
          </td>
    
        </tr>
        @endforeach

      </tbody>
 
    </table>

  </div>
<div class="d-flex justify-content-center">
    {{ $users->links() }}
</div>
</div>


@endsection

