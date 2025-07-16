<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
     use HasFactory;

    protected $fillable = 
    ['order_id', 'payment_method', 'transaction_id', 'amount', 'currency', 'status', 'paid_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getPaymentMethodTextAttribute()
    {
      $methods = [
        'cash' => 'نقدا',
        'card' => 'بالبطاقة',
        
      ];

      return $methods[$this->payment_method] ?? $this->payment_method;
    }

}
