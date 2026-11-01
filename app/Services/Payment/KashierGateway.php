<?php

namespace App\Services\Payment;

class KashierGateway
{
    private const CURRENCY = 'EGP';

    // The fields Kashier signs in the redirect, in the exact order given in its documentation
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

    /**
     * Everything the Kashier payment page needs, including the signature of the order.
     */
    public function paymentPageData(string $merchantOrderId, int|float|string $amount): array
    {
        $merchantId = config('services.kashier.merchant_id');
        $path = "/?payment={$merchantId}.{$merchantOrderId}.{$amount}." . self::CURRENCY;

        return [
            'merchantId' => $merchantId,
            'orderId' => $merchantOrderId,
            'amount' => $amount,
            'currency' => self::CURRENCY,
            'mode' => config('services.kashier.mode', 'test'),
            'hash' => hash_hmac('sha256', $path, $this->apiKey()),
            'redirect_url' => route('payment.callback'),
        ];
    }

    /**
     * Is the redirect really signed by Kashier?
     */
    public function isGenuine(array $query): bool
    {
        $signature = $query['signature'] ?? '';

        if (!is_string($signature) || $signature === '') {
            return false;
        }

        $parts = [];

        foreach (self::SIGNED_FIELDS as $field) {
            // A field missing from the redirect is signed as the text "null"
            $value = $query[$field] ?? 'null';

            if (!is_scalar($value)) {
                return false;
            }

            $parts[] = "{$field}={$value}";
        }

        $expected = hash_hmac('sha256', implode('&', $parts), $this->apiKey());

        return hash_equals($expected, strtolower($signature));
    }

    public function isSuccessful(array $query): bool
    {
        $status = $query['paymentStatus'] ?? '';

        return is_scalar($status) && strtoupper((string) $status) === 'SUCCESS';
    }

    /**
     * Does this payment belong to the current checkout (same order id and same amount)?
     */
    public function matchesCheckout(array $query, string $merchantOrderId, float $amount): bool
    {
        $paidAmount = (float) ($query['amount'] ?? 0);

        return ($query['merchantOrderId'] ?? null) === $merchantOrderId
            && abs($paidAmount - $amount) <= 0.01;
    }

    private function apiKey(): string
    {
        return (string) config('services.kashier.api_key');
    }
}