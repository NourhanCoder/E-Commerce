<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders)
    {
        //
    }
    public function index()
    {
        $orders = Order::with(['user'])->latest()->paginate(10);
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

        try {
            $this->orders->changeStatus($order, OrderStatus::from($validated['status']));
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withErrors([
                'error' => __('orders.out_of_stock', ['title' => $e->product->title]),
            ]);
        }

        return redirect()->route('admin.orders.index')->with('success', __('orders.status_updated'));
    }
}
