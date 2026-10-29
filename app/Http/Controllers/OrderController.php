<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index()
{
    // Retrieve orders for authenticated user
    $orders = Order::with('payment')
        ->where('user_id', Auth::id())
        ->latest()
        ->get();

   
    return view('website.orders.userOrders', compact('orders'));
}

public function show(Order $order)
{
    Gate::authorize('view', $order);

    $order->load(['orderItems.product', 'address', 'payment']);

    return view('website.orders.showOrder', compact('order'));
}

}
