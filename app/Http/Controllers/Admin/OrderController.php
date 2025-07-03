<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user'])->latest()->paginate(10); // عرض مع المستخدم المرتبط
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
    return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
        'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

       return  redirect()->route('admin.orders.index')->with('success', 'Status Updated Successfully');
    }
}
