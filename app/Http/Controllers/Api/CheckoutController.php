<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class CheckoutController extends Controller
{
    public function index()
{
    $user = auth()->user();

    $cartItems = $user->cartItems()->with('product')->get();

    $cart = $cartItems->map(function ($item) {
        return [
            'product_id' => $item->product_id,
            'title' => $item->product->title,
            'price' => $item->product->discounted_price ?? $item->product->price,
            'quantity' => $item->quantity,
            'subtotal' => ($item->product->discounted_price ?? $item->product->price) * $item->quantity,
        ];
    });

    $total = $cart->sum('subtotal');

    return response()->json([
        'cart' => $cart,
        'total' => $total,
    ]);
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

        // البطاقة – رجعي رابط صفحة الكاشير
        return response()->json([
            'message' => 'اذهب لبوابة الدفع',
            'redirect_url' => route('payment.iframe'),
        ]);
    }

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
                if (!$product || $product->stock < $item['quantity']) {
                    throw new \Exception("الكمية غير متوفرة للمنتج: {$product?->title}");
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

            // مسح السلة من قاعدة البيانات فقط (لا يوجد session في الـ API)
            $user->cartItems()->delete();

            DB::commit();

            return (new OrderResource($order->load(['orderItems.product', 'address', 'payment'])))
                ->additional([
                    'message' => 'تم إنشاء الطلب بنجاح.'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'pending') {
            return response()->json([
                'message' => 'غير مسموح لك بإلغاء هذا الطلب.'
            ], 403);
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

            return (new OrderResource($order->load(['orderItems.product', 'address', 'payment'])))
                ->additional([
                    'message' => 'تم إلغاء الطلب وإرجاع الكمية بنجاح.'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'حدث خطأ أثناء إلغاء الطلب.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
