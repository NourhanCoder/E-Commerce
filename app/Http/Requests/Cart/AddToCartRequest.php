<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $stock = $this->route('product')->stock;
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $stock],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => __('cart.quantity_required'),
            'quantity.integer' => __('cart.quantity_invalid'),
            'quantity.min' => __('cart.quantity_min'),
            'quantity.max' => __('cart.quantity_max', [
                'stock' => $this->route('product')->stock,
            ]),
        ];
    }
}
