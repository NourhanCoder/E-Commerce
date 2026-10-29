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
     * Early check, before the payment page. placeOrder checks again with a lock.
     *
     * @throws InsufficientStockException
     */
    public function assertInStock(array $cart): void
    {
        foreach ($cart as $productId => $item) {
            $this->ensureEnoughStock(Product::findOrFail($productId), $item['quantity']);
        }
    }

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

            foreach ($cart as $productId => $item) {
                // Lock the product row until the transaction ends
                $product = Product::lockForUpdate()->findOrFail($productId);

                $this->ensureEnoughStock($product, $item['quantity']);

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

    /**
     * Change the status of an order and keep the stock in line with it.
     * Cancelling gives the quantities back; re-opening a cancelled order takes them again.
     *
     * @throws InsufficientStockException
     */

    public function changeStatus(Order $order, OrderStatus $newStatus): void
    {
        DB::transaction(function () use ($order, $newStatus) {
            // Re-read the order with a lock so two changes can never overlap
            $order = Order::lockForUpdate()->findOrFail($order->id);
            $oldStatus = $order->status;

            if ($this->holdsStock($oldStatus) && $newStatus === OrderStatus::Cancelled) {
                $this->restoreStock($order);
            }

            if ($oldStatus === OrderStatus::Cancelled && $this->holdsStock($newStatus)) {
                $this->takeStock($order);
            }

            $order->update(['status' => $newStatus]);
        });
    }

    // Same rule the admin panel used before: these statuses are the ones that hold stock
    private function holdsStock(OrderStatus $status): bool
    {
        return in_array($status, [OrderStatus::Pending, OrderStatus::Processing, OrderStatus::Shipped], true);
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->orderItems->sortBy('product_id') as $item) {
            // The book may have been deleted since the order was placed
            Product::lockForUpdate()->find($item->product_id)?->increment('stock', $item->quantity);
        }
    }

    private function takeStock(Order $order): void
    {
        foreach ($order->orderItems->sortBy('product_id') as $item) {
            $product = Product::lockForUpdate()->findOrFail($item->product_id);

            $this->ensureEnoughStock($product, $item->quantity);

            $product->decrement('stock', $item->quantity);
        }
    }

    private function ensureEnoughStock(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new InsufficientStockException($product);
        }
    }
}
