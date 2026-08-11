<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

// ==========================================
// 1. Local Scopes (to improve queries in the controller)
// ==========================================

// Gets the product with its active discount
public function scopeWithActiveDiscount(Builder $query)
{
    return $query->with(['discount' => function ($q) {
        $q->active();
    }]);
}

// Gets the best-selling products
public function scopeBestSelling(Builder $query, int $minSold = 3, int $limit = 10)
{
    return $query->withCount(['orderItems as total_sold' => function ($q) {
        $q->select(DB::raw('SUM(quantity)'));
    }])
    ->having('total_sold', '>', '$minSold')
    ->orderByDesc('total_sold')
    ->take($limit);
}


// ==========================================
// 2. Accessors (to get calculated values in Views)
// ==========================================

// Gets the best-selling products
public function getActiveDiscountAttribute()
{
    // If the discount is already loaded with with()
    if ($this->relationLoaded('discount') && $this->discount){
        $isDiscountVaild = $this->discount->start_date <= now() && $this->discount->end_date >= now();
        return $isDiscountVaild ? $this->discount : null;
    }

    // If not loaded, get it with one query using the active scope
    return $this->discount()->active()->first();
}

// Calculate the price after discount
public function getDiscountedPriceAttribute()
{
    $discount = $this->active_discount; // Calls the Accessor above

    if ($discount) {
        return round($this->price * (1 - $discount->percentage / 100), 2);
    }

    return $this->price;
}


// //لتفادي تكرار كود الخصم في الصفحات او الكنترولر
// public function getDiscountedPriceAttribute()
// {
//     $discount = null;

//     // لو الخصم محمل بالفعل
//     if ($this->relationLoaded('discount') && $this->discount) {
//         if (
//             $this->discount->start_date <= now() &&
//             $this->discount->end_date >= now()
//         ) {
//             $discount = $this->discount;
//         }
//     } else {
//         // خصم من استعلام يدوي
//        $discount = $this->discount()
//         ->where('start_date', '<=', now())
//         ->where('end_date', '>=', now())
//         ->first();
//       }

//     if ($discount) {
//         return round($this->price * (1 - $discount->percentage / 100), 2);
//     }

//     return $this->price;
// }

// //لمعرفة الخصم الحالي ونسبته
// public function getActiveDiscountAttribute()
// {
//     // لو الخصم متحمّل بالفعل من with()
//     //لتحسين اداء الموقع
//     if ($this->relationLoaded('discount') && $this->discount) {
//         if (
//             $this->discount->start_date <= now() &&
//             $this->discount->end_date >= now()
//         ) {
//             return $this->discount;
//         }
//     }
//     return $this->discount()
//         ->where('start_date', '<=', Carbon::now()) //كلاس كاربون سهل الاستخدام لاداراة التواريخ والوقت
//         ->where('end_date', '>=', Carbon::now())
//         ->first();
// }

}
