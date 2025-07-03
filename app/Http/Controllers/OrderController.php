<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
{
    // نجيب الطلبات الخاصة بالمستخدم الحالي
    $orders = Order::with('payment')
        ->where('user_id', Auth::id())
        ->latest()
        ->get();

   
    return view('website.orders.userOrders', compact('orders'));
}

public function show(Order $order)
{
    if ($order->user_id !== Auth::id()) {
        abort(403, 'غير مسموح بعرض هذا الطلب.');
    }

    $order->load(['orderItems.product', 'address', 'payment']);

    return view('website.orders.showOrder', compact('order'));
}

}
