<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    // Restrict order view access to the authenticated owner
    public function view(User $user, Order $order): Response
    {
        return $user->id === $order->user_id
        ? Response::allow()
        : Response::deny(__('orders.view_forbidden'));
    }

    // Allow user to cancel order only if pending
    public function cancel(User $user, Order $order): Response
    {
        return $user->id === $order->user_id && $order->status === OrderStatus::Pending
        ? Response::allow()
        : Response::deny(__('orders.cancel_forbidden'));
    }
}