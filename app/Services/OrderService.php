<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Exceptions\InsufficientStockException;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create the order, its items and its payment, and update the stock.
     * Everything succeeds together or nothing is saved.
     *
     * @throws InsufficientStockException
     */

    public function placeOrder(
        User $user,
        array $addressData,
        array $cart,
        float $total,
        PaymentMethod $paymentMethod,
        array $paymentData = [],
    ): Order {
        //lock products in the same order (by id) so two orders never wait for each other
        ksort($cart);

        return DB::transaction(function () use ($user, $addressData, $cart, $total, $paymentMethod, $paymentData) {
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
                'status' => OrderStatus::Pending,
            ]);

            foreach($cart as $productId => $item){
                // Lock the product row until the transaction ends
                $product = Product::lockForUpdate()->findOrFail($productId);

                if($product->stock < $item['quantity']){
                    throw new InsufficientStockException($product);
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
                'payment_method' => $paymentMethod,
                'amount' => $total,
                ...$paymentData,                   //Spread Operator
            ]);

            $user->cartItems()->delete();

            //the success page shows the address of this order
            $order->setRelation('address', $address);

            return $order;
        });
    }
}