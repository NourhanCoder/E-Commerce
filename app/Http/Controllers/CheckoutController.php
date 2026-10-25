<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\InsufficientStockException;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orders)
    {
        //
    }
    public function index()
    {
        $user = Auth::user();

        $cartItems = $user->cartItems()->with('product')->get();

        $cart = [];
        $total = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            $price = $product->discounted_price ?? $product->price;
            $cart[$product->id] = [
                'title' => $product->title,
                'price' => $price,
                'image' => $product->image,
                'quantity' => $item->quantity,
            ];
            $total += $price * $item->quantity;
        }
        return view('website.orders.checkOut', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string|max:1000',
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
        ]);

        $user = Auth::user();

        //لحفظ عنوان الشحن مؤقتا في حالة الدفع بالكارت لمنع طلب البيانات من المستخدم مرة اخرى
        session()->put('checkout.address', $validated);

        $cart = $user->cartItems()->with('product')->get()->keyBy('product_id')->map(function ($item) {
            return [
                'title' => $item->product->title,
                'price' => $item->product->discounted_price ?? $item->product->price,
                'image' => $item->product->image,
                'quantity' => $item->quantity,
            ];
        })->toArray();

        if (empty($cart)) {
            return redirect()->back()->withErrors(['cart' => 'السلة فارغة']);
        }

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // تخزين في السيشن علشان نرجعلهم بعد الدفع
        session()->put('checkout.cart', $cart);
        session()->put('checkout.total', $total);

        //لو نقدي هيكمل الطلب
        if (PaymentMethod::from($validated['payment_method']) === PaymentMethod::Cash) {
            return $this->handleCashCheckout($validated, $cart, $total);
        }

        // لو بطاقه هيروح على صفحة كاشير
        return redirect()->route('payment.iframe');
    }


    // Cash checkout: no payment gateway is involved
    protected function handleCashCheckout(array $addressData, array $cart, float $total)
    {
        try {
            $order = $this->orders->placeOrder(
                Auth::user(),
                $addressData,
                $cart,
                $total,
                PaymentMethod::Cash,
                ['status' => 'pending'], //spread operator data
            );

        } catch (InsufficientStockException $e) {
            return redirect()->back()->withErrors([
                'error' => __('orders.out_of_stock', ['title' => $e->product->title]),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->withErrors(['error' => __('orders.checkout_failed')]);
        }

        session()->forget(['checkout.address', 'checkout.cart', 'checkout.total', 'checkout.order_id']);

        return view('website.orders.orderSuccess', [
            'order' => $order,
            'address' => $order->address,
        ]);
    }



    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== OrderStatus::Pending) {
            abort(403, 'غير مسموح لك بإلغاء هذا الطلب.');
        }

        DB::beginTransaction();

        try {
            // استرجاع الكمية لكل منتج في الطلب
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            // تغيير حالة الطلب إلى ملغي
            $order->update(['status' => OrderStatus::Cancelled]);

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'تم إلغاء الطلب وإرجاع الكمية بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء إلغاء الطلب.']);
        }
    }
}
