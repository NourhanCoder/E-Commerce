<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Exceptions\InsufficientStockException;
use App\Services\OrderService;
use App\Services\Payment\KashierGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(private OrderService $orders, private KashierGateway $kashier)
    {
        //
    }
    public function getPaymentIframe()
    {
        // Nothing to pay for: the customer did not come from the checkout form
        if (!session()->has('checkout.total')) {
            return redirect()->route('checkout.index');
        }

        $merchantOrderId = (string) Str::uuid();

        session()->put('checkout.order_id', $merchantOrderId);

        return view('payment.iframe', $this->kashier->paymentPageData($merchantOrderId, session('checkout.total')));
    }

    public function paymentCallback(Request $request)
    {
        $query = $request->query();

        // 1) The response must really come from Kashier
        if (!$this->kashier->isGenuine($query)) {
            Log::warning('Invalid Kashier signature on payment callback', ['user_id' => Auth::id()]);

            return redirect()->route('checkout.index')->withErrors(['error' => __('orders.payment_invalid')]);
        }

        // 2) The payment must have succeeded
        if (!$this->kashier->isSuccessful($query)) {
            return redirect()->route('checkout.index')->withErrors(['error' => __('orders.payment_failed')]);
        }

        $cart = session('checkout.cart');
        $total = session('checkout.total');
        $addressData = session('checkout.address');
        $orderUUID = session('checkout.order_id');

        if (!$cart || !$total || !$addressData || !$orderUUID) {
            return redirect()->route('checkout.index')->withErrors(['error' => __('orders.payment_incomplete')]);
        }

        // 3) The payment must belong to this checkout
        if (!$this->kashier->matchesCheckout($query, $orderUUID, (float) $total)) {
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
                    'status' => $request->query('paymentStatus'),
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
