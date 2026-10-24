<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['total_price', 'status', 'user_id'];

     protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function address()
    {
       return $this->belongsTo(Address::class, 'user_id', 'user_id');
    }

    public function getStatusTextAttribute(): string
    {
      return $this->status->label();
    }

}
