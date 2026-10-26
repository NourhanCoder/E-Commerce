<?php

namespace App\Services\Payment;

class KashierSignatureValidator
{
    private const SIGNED_FIELDS = [
        'paymentStatus',
        'cardDataToken',
        'maskedCard',
        'merchantOrderId',
        'orderId',
        'cardBrand',
        'orderReference',
        'transactionId',
        'amount',
        'currency',
    ];

    public function isVaild(array $query): bool
    {
        $signature = $query['signature'] ?? '';

        if (!is_string($signature) || $signature === ''){
            return false;

        }

        $parts = [];

        foreach (self::SIGNED_FIELDS as $field){
            $value = $query[$field] ?? 'null';

            if (!is_scalar($value)) {
                return false;
            }

            $parts[] = "{$field}={$value}";
        }

        $expected = hash_hmac('sha256', implode('&', $parts), (string) config('services.kashier.api_key'));

        return hash_equals($expected, strtolower($signature));

    }
}