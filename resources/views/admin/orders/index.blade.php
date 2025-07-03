@extends('admin.layouts.app')

@section('title', 'Orders List')

@section('admin-content')
<div class="right_col" role="main">
  <div class="container">
    <h2>Orders List</h2>

    @if (session('success'))
      <div class="alert alert-success mt-3">
        {{ session('success') }}
      </div>
    @endif

    <table class="table table-bordered table-striped text-center">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>User Name</th>
          <th>Email</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th>Details</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $index => $order)
          <tr>
            <td>{{ $orders->firstItem() + $index }}</td>
            <td>{{ $order->user->name ?? 'UnKnown' }}</td>
            <td>{{ $order->user->email ?? 'UnKnown' }}</td>
            <td>{{ number_format($order->total_price, 2) }} EGP</td>
            <td>
              @switch($order->status)
                @case('pending')
                  <span class="badge bg-warning text-dark">Pending</span>
                  @break
                  @case('processing')
                  <span class="badge bg-success">Processing</span>
                  @break
                @case('shipped')
                  <span class="badge bg-success">Shipped</span>
                  @break
                   @case('delivered')
                  <span class="badge bg-success">Delivered</span>
                  @break
                @case('cancelled')
                  <span class="badge bg-danger">Cancelled</span>
                  @break
                @default
                  <span class="badge bg-secondary">UnKnown</span>
              @endswitch
            </td>
            <td>{{ $order->created_at->format('Y-m-d') }}</td>
            <td>
              <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Show</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7">There are no orders</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="d-flex justify-content-center">
      {{ $orders->links() }}
    </div>
  </div>
</div>
@endsection
