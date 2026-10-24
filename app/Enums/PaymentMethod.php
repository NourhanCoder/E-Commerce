<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';

    // Retrieve Arabic name from language file
    public function label(): string
    {
        return __('orders.payment_method.' . $this->value);
    }
}