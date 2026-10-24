@extends('admin.layouts.app')
@use('App\Enums\OrderStatus')
@use('App\Enums\PaymentMethod')

@section('title', 'Order Details')
@section('admin-content')

    <div class="right_col" role="main">
        <div class="container">
            <h2>Order Details Number #{{ $order->id }}</h2>

            {{-- Customer info --}}
            <div class="card p-4 mb-4">
                <h4>Customer Information</h4>
                <p><strong>Name :</strong> {{ $order->user->name }}</p>
                <p><strong>Email :</strong> {{ $order->user->email }}</p>
            </div>

            {{-- Shipping Address --}}
            <div class="card p-4 mb-4">
                <h4>Shipping Address</h4>
                <p><strong>City :</strong> {{ $order->address->city }}</p>
                <p><strong>street :</strong> {{ $order->address->street }}</p>
                <p><strong>Phone Number :</strong> {{ $order->address->phone }}</p>
                <p><strong>Note :</strong> {{ $order->address->note ?? 'لا يوجد' }}</p>
            </div>

            {{-- Payment Methods --}}
            <div class="card p-4 mb-4">
                <h4>Payment Method</h4>
                <p>{{ $order->payment->payment_method === PaymentMethod::Cash ? 'Cash on Delivery' : 'Card' }}</p>
            </div>

            {{-- Order status + Ability to Change --}}
            <div class="card p-4 mb-4">
                <h4>Current Order Status <span class="badge bg-warning bg-info text-dark">{{ $order->status->value }}</span>
                </h4>

                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('PATCH')
                    <label for="status">Change Order Status :</label>
                    <select name="status" id="status" class="form-control w-25 mb-2">
                        @foreach (OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>
                                {{ ucfirst($status->value) }}
                            </option>
                        @endforeach
                        </select>
                        <button class="btn btn-primary">Update Status</button>
                </form>
            </div>

            {{-- Order Products --}}
            <div class="card p-4">
                <h4>Products In The Order</h4>
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price At Order</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product->title }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price_at_order, 2) }} EGP</td>
                                <td>{{ number_format($item->price_at_order * $item->quantity, 2) }} EGP</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3"><strong>Total Price</strong></td>
                            <td><strong>{{ number_format($order->total_price, 2) }} EGP</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
