<?php

namespace App\Http\Requests\Checkout;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The checkout routes already require a logged-in user
        return true;
    }

    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }
}