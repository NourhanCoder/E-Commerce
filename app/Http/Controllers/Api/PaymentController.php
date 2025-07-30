<?php

namespace App\Http\Controllers\Api;


use App\Models\Order;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function getPaymentIframe()
{
    $merchantId = config('services.kashier.merchant_id');
    $apiKey = config('services.kashier.api_key');

    $orderId = (string) Str::uuid();
    $amount = session('checkout.total');
    $currency = 'EGP';
    $mode = 'test';
    $customerReference = Auth::id();
    $redirect_url = route('api.payment.callback', ['orderId' => $orderId]);

    $path = "/?payment=$merchantId.$orderId.$amount.$currency";
    $hash = hash_hmac('sha256', $path, $apiKey);

    session()->put('checkout.order_id', $orderId);

    return response()->json([
        'merchantId' => $merchantId,
        'orderId' => $orderId,
        'amount' => $amount,
        'currency' => $currency,
        'mode' => $mode,
        'hash' => $hash,
        'redirect_url' => $redirect_url,
    ]);
}

public function paymentCallback(Request $request)
{
    if (!in_array($request->paymentStatus, ['SUCCESS', 'success'])) {
        return response()->json([
            'message' => 'فشل في عملية الدفع.',
            'status' => 'error',
        ], 400);
    }

    $cart = session('checkout.cart');
    $total = session('checkout.total');
    $addressData = session('checkout.address');
    $orderUUID = session('checkout.order_id');

    if (!$cart || !$total || !$addressData || !$orderUUID) {
        return response()->json([
            'message' => 'بيانات الدفع غير مكتملة.',
            'status' => 'error',
        ], 400);
    }

    DB::beginTransaction();

    try {
        $user = Auth::user();

        $address = Address::create([
            'user_id' => $user->id,
            'city' => $addressData['city'],
            'street' => $addressData['street'],
            'phone' => $addressData['phone'],
            'note' => $addressData['note'] ?? null,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $total,
            'status' => 'pending',
        ]);

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product || $product->stock < $item['quantity']) {
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

        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'card',
            'amount' => $total,
            'status' => $request->paymentStatus,
            'paid_at' => now(),
            'transaction_id' => $request->transactionId,
        ]);

        if (Auth::check()) {
            $user->cartItems()->delete();
        } else {
            session()->forget('cart');
        }

        session()->forget(['checkout.address', 'checkout.cart', 'checkout.total', 'checkout.order_id']);

        DB::commit();

        return response()->json([
            'message' => 'تم إنشاء الطلب بنجاح.',
            'order_id' => $order->id,
            'status' => 'success',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'فشل أثناء إتمام الطلب بعد الدفع.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
