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

    $oldStatus = $order->status;
    $newStatus = $request->status;

    // لو اتحول من pending/cancelled إلى cancelled  نرجع الكمية
    if (in_array($oldStatus, ['pending', 'processing', 'shipped']) && $newStatus == 'cancelled') {
        foreach ($order->orderItems as $item) {
            $item->product->increment('stock', $item->quantity);
        }
    }

    // لو اتحول من cancelled إلى حالة تانية  نخصم الكمية تاني
    if ($oldStatus == 'cancelled' && in_array($newStatus, ['pending', 'processing', 'shipped'])) {
        foreach ($order->orderItems as $item) {
            // تأكد إن الكمية متوفرة
            if ($item->product->stock < $item->quantity) {
                return redirect()->back()->withErrors(['error' => "الكمية غير متوفرة للمنتج: {$item->product->title}"]);
            }
            $item->product->decrement('stock', $item->quantity);
        }
    }

    $order->update(['status' => $newStatus]);

    return redirect()->route('admin.orders.index')->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }
}
