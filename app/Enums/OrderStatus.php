<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    // Retrieve Arabic name from language file
    public function label(): string
    {
        return __('orders.status.' . $this->value);
    }
}