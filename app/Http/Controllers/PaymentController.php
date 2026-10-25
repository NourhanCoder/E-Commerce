<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Exceptions\InsufficientStockException;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(private OrderService $orders)
    {
        //
    }
    public function getPaymentIframe()
    {
        $merchantId = config('services.kashier.merchant_id');
        $apiKey = config('services.kashier.api_key');

        $orderId = (string) Str::uuid();
        $amount = session('checkout.total');
        $currency = 'EGP';
        $mode = 'test';
        $redirect_url = route('payment.callback', ['orderId' => $orderId]);

        $path = "/?payment=$merchantId.$orderId.$amount.$currency";
        $hash = hash_hmac('sha256', $path, $apiKey);

        session()->put('checkout.order_id', $orderId);

        return view('payment.iframe', compact(
            'merchantId',
            'orderId',
            'amount',
            'currency',
            'mode',
            'hash',
            'redirect_url'
        ));
    }

    public function paymentCallback(Request $request)
    {
        if (!in_array($request->paymentStatus, ['SUCCESS', 'success'])) {
            return redirect()->route('checkout.index')->withErrors(['error' => 'فشل في عملية الدفع.']);
        }

        $cart = session('checkout.cart');
        $total = session('checkout.total');
        $addressData = session('checkout.address');
        $orderUUID = session('checkout.order_id');

        if (!$cart || !$total || !$addressData || !$orderUUID) {
            return redirect()->route('checkout.index')->withErrors(['error' => 'بيانات الدفع غير مكتملة.']);
        }


        try {
            $order = $this->orders->placeOrder(
                Auth::user(),
                $addressData,
                $cart,
                $total,
                PaymentMethod::Card,
                [
                    'status' => $request->paymentStatus,
                    'paid_at' => now(),
                    'transaction_id' => $request->transactionId,
                ],
            );
        } catch (InsufficientStockException $e) {
            return redirect()->route('checkout.index')->withErrors([
                'error' => __('orders.out_of_stock_after_payment', ['title' => $e->product->title]),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('checkout.index')->withErrors(['error' => __('orders.checkout_failed')]);
        }

        session()->forget(['checkout.address', 'checkout.cart', 'checkout.total', 'checkout.order_id']);

        return view('website.orders.orderSuccess', [
            'order' => $order,
            'address' => $order->address,
        ]);
    }
}
