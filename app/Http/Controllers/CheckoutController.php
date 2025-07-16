<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);


        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('website.orders.checkOut', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,card',
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
        if ($validated['payment_method'] === 'cash') {
            return $this->handleCashCheckout($validated, $cart, $total);
        }

        // لو بطاقه هيروح على صفحة كاشير
        return redirect()->route('payment.iframe');
    }


    //لتنفيذ عملية الشراء نقدا من غير المرور على بوابة دفع كاشير
    protected function handleCashCheckout($addressData, $cart, $total)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            // حفظ العنوان
            $address = Address::create([
                'user_id' => $user->id,
                'city' => $addressData['city'],
                'street' => $addressData['street'],
                'phone' => $addressData['phone'],
                'note' => $addressData['note'] ?? null,
            ]);

            // إنشاء الطلب
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $total,
                'status' => 'pending',
            ]);

            // حفظ عناصر الطلب وخصم المخزون
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("الكمية غير متوفرة للمنتج: {$product->title}");
                }

                $product->decrement('stock', $item['quantity']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price_at_order' => $item['price'],
                ]);
            }

            // وسيلة الدفع
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'cash',
                'amount' => $total,
                'status' => 'pending',
            ]);

            // مسح السلة
            if (Auth::check()) {
                $user->cartItems()->delete();
            } else {
                session()->forget('cart');
            }

            session()->forget(['checkout.address', 'checkout.cart', 'checkout.total', 'checkout.order_id']);

            DB::commit(); //لحفظ البيانات و إنهاء العملية

            return view('website.orders.orderSuccess', compact('order', 'address'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'pending') {
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
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('checkout.index')->with('success', 'تم إلغاء الطلب وإرجاع الكمية بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء إلغاء الطلب.']);
        }
    }
}
