<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\CartService;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\Checkout\CheckoutRequest;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orders, private CartService $cart)
    {
        //
    }
    public function index()
    {
        ['cart' => $cart, 'total' => $total] = $this->cart->items();

        return view('website.orders.checkOut', compact('cart', 'total'));
    }

    public function store(CheckoutRequest $request)
    {
        $validated = $request->validated();

        ['cart' => $cart, 'total' => $total] = $this->cart->items();

        if (empty($cart)) {
            return redirect()->back()->withErrors(['cart' => __('orders.cart_empty')]);
        }
        // Cache shipping address for card checkout to avoid re-prompting user
        session()->put('checkout.address', $validated);

        // Store data in session to retrieve after payment completion
        session()->put('checkout.cart', $cart);
        session()->put('checkout.total', $total);

        // Proceed with order for cash payment
        if (PaymentMethod::from($validated['payment_method']) === PaymentMethod::Cash) {
            return $this->handleCashCheckout($validated, $cart, $total);
        }

        // Card: make sure everything is still in stock before sending the customer to the payment page
        try {
            $this->orders->assertInStock($cart);
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withErrors([
                'error' => __('orders.out_of_stock', ['title' => $e->product->title]),
            ]);
        }

        // Redirect to Kashier for card payment
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
        Gate::authorize('cancel', $order);

        try{
            $this->orders->changeStatus($order, OrderStatus::Cancelled);
        } catch (\Throwable $e){
            report($e);

            return redirect()->back()->withErrors(['error' => __('orders.cancel_failed')]);
        }
        
        return redirect()->route('orders.index')->with('success', __('orders.cancelled'));
        
    }
}
