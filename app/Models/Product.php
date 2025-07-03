<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'description', 'price', 'stock', 'sku', 'image', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function discount()
    {
        return $this->hasOne(Discount::class);
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function image()
{
    return $this->image ? asset('public/products/' . $this->image) : null;
}


//لتفادي تكرار كود الخصم في الصفحات او الكنترولر
public function getDiscountedPriceAttribute()
{
    $discount = $this->discount()
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->first();

    if ($discount) {
        return round($this->price * (1 - $discount->percentage / 100), 2);
    }

    return $this->price;
}

//لمعرفة الخصم الحالي ونسبته
public function getActiveDiscountAttribute()
{
    return $this->discount()
        ->where('start_date', '<=', Carbon::now()) //كلاس كاربون سهل الاستخدام لاداراة التواريخ والوقت
        ->where('end_date', '>=', Carbon::now())
        ->first();
}

}
