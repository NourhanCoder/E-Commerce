<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Exceptions\InsufficientStockException;
use App\Services\OrderService;
use App\Services\Payment\KashierSignatureValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $redirect_url = route('payment.callback');

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

    public function paymentCallback(Request $request, KashierSignatureValidator $kashier)
    {
        // 1) The response must really come from Kashier
        if (!$kashier->isVaild($request->query())) {
            Log::warning('Invalid Kashier signature on payment callback', ['user_id' => Auth::id()]);

            return redirect()->route('checkout.index')->withErrors(['error' => __('orders.payment_invalid')]);
        }

        // 2) The payment must have succeeded
        if (strtoupper((string) $request->query('paymentStatus')) !== 'SUCCESS') {
            return redirect()->route('checkout.index')->withErrors(['error' => __('orders.payment_failed')]);
        }

        $cart = session('checkout.cart');
        $total = session('checkout.total');
        $addressData = session('checkout.address');
        $orderUUID = session('checkout.order_id');

        if (!$cart || !$total || !$addressData || !$orderUUID) {
            return redirect()->route('checkout.index')->withErrors(['error' => __('orders.payment_incomplete')]);
        }

         // 3) The payment must belong to this checkout (same order id and same amount)
         $paidAmount = (float) $request->query('amount');

        if ($request->query('merchantOrderId') !== $orderUUID || abs($paidAmount - (float) $total) > 0.01) {
            Log::warning('Kashier payment does not match the checkout', ['user_id' => Auth::id()]);

            return redirect()->route('checkout.index')->withErrors(['error' => __('orders.payment_mismatch')]);
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
                    'transaction_id' => $request->query('transactionId'),
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
