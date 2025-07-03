@extends('admin.layouts.app')

@section('title', 'Order Details')
@section('admin-content')

<div class="right_col" role="main">
    <div class="container">
        <h2>Order Details Number #{{ $order->id }}</h2>

        {{-- معلومات العميل --}}
        <div class="card p-4 mb-4">
            <h4>Customer Information</h4>
            <p><strong>Name :</strong> {{ $order->user->name }}</p>
            <p><strong>Email :</strong> {{ $order->user->email }}</p>
        </div>

        {{-- عنوان الشحن --}}
        <div class="card p-4 mb-4">
            <h4>Shipping Address</h4>
            <p><strong>City :</strong> {{ $order->address->city }}</p>
            <p><strong>streat :</strong> {{ $order->address->street }}</p>
            <p><strong>Phone Number :</strong> {{ $order->address->phone }}</p>
            <p><strong>Note :</strong> {{ $order->address->note ?? 'لا يوجد' }}</p>
        </div>

        {{-- وسيلة الدفع --}}
        <div class="card p-4 mb-4">
            <h4>Payment Method</h4>
            <p>{{ $order->payment->payment_method == 'cash' ? 'Cash on Delivery' : 'Card' }}</p>
        </div>

        {{-- حالة الطلب + إمكانية التعديل --}}
        <div class="card p-4 mb-4">
            <h4>Current Order Status <span class="badge bg-warning bg-info text-dark">{{ $order->status }}</span></h4>

            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mt-3">
                @csrf
                @method('PATCH')
                <label for="status">Change Order Status :</label>
                <select name="status" id="status" class="form-control w-25 mb-2">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button class="btn btn-primary">Update Status</button>
            </form>
        </div>

        {{-- المنتجات داخل الطلب --}}
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
                    @foreach($order->orderItems as $item)
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
