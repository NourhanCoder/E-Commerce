<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $validated = $request->validate([
        'status' => ['required', Rule::enum(OrderStatus::class)],
    ]);

    $oldStatus = $order->status;
    $newStatus = OrderStatus::from($validated['status']);

    $activeStatuses = [OrderStatus::Pending, OrderStatus::Processing, OrderStatus::Shipped];

    // Cancelling an active order: give the quantities back to the stock
    if (in_array($oldStatus, $activeStatuses, true) && $newStatus == OrderStatus::Cancelled) {
        foreach ($order->orderItems as $item) {
            $item->product->increment('stock', $item->quantity);
        }
    }

    // Re-activating a cancelled order: take the quantities from the stock again
    if ($oldStatus == OrderStatus::Cancelled && in_array($newStatus, $activeStatuses, true)) {
        foreach ($order->orderItems as $item) {
            
            // Verify stock availability
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
