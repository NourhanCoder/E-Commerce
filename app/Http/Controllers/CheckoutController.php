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

        //  نبدأ المعاملة (transaction) لضمان سلامة العملية
        DB::beginTransaction();

        try {
            $user = Auth::user();

            //  نحفظ العنوان في جدول addresses
            $address = Address::create([
                'user_id' => $user->id,
                'city' => $validated['city'],
                'street' => $validated['street'],
                'phone' => $validated['phone'],
                'note' => $validated['note'] ?? null,

            ]);

            //  نجيب المنتجات من السلة (سيشن أو داتابيز)
            $cart = Auth::check()
                ? $user->cartItems()->with('product')->get()->keyBy('product_id')->map(function ($item) {
                    return [
                        'title' => $item->product->title,
                        'price' => $item->product->discounted_price ?? $item->product->price,
                        'image' => $item->product->image,
                        'quantity' => $item->quantity,
                    ];
                })->toArray()
                : session()->get('cart', []);

            if (empty($cart)) {
                return redirect()->back()->withErrors(['cart' => 'السلة فارغة']);
            }

            //  نحسب السعر الإجمالي
            $total = collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            //  نحفظ الطلب نفسه
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $total,
                'status' => 'pending',
            ]);

            //   نحفظ كل منتج في order_items
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);

                // التحقق من توفر الكمية
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("الكمية غير متوفرة للمنتج: {$product->title}");
                }

                // خصم الكمية
                $product->decrement('stock', $item['quantity']);

                // إنشاء عنصر الطلب
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price_at_order' => $item['price'],
                ]);
            }


            // حفظ وسيلة الدفع
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
            ]);

            //  نحذف محتوى السلة من الداتابيز أو السيشن
            if (Auth::check()) {
                $user->cartItems()->delete();
            } else {
                session()->forget('cart');
            }

            DB::commit();

            //  نعرض صفحة النجاح مباشرة مع البيانات
            return view('website.orders.orderSuccess', compact('order', 'address'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' =>  $e->getMessage()]);
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

        // تغيير حالة الطلب إلى "ملغي"
        $order->update(['status' => 'cancelled']);

        DB::commit();

        return redirect()->route('checkout.index')->with('success', 'تم إلغاء الطلب وإرجاع الكمية بنجاح.');
       }catch (\Exception $e) {
          DB::rollBack();
          return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء إلغاء الطلب.']);
       }
    }
}
