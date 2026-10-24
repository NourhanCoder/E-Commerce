<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
     use HasFactory;

    protected $fillable = 
    ['order_id', 'payment_method', 'transaction_id', 'amount', 'currency', 'status', 'paid_at'];

    protected function casts(): array
    {
      return [
        'payment_method' => PaymentMethod::class,
      ];

    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getPaymentMethodTextAttribute(): string
    {

      return $this->payment_method->label();

    }

}
