<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'price',
        'stock',
        'sku',
        'image',
        'category_id'
    ];

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

    // Gets the best-selling books (cancelled orders are not counted)
    public function scopeBestSelling(Builder $query, int $minSold = 7, int $limit = 10)
    {
        return $query->withCount(['orderItems as total_sold' => function ($q) {
            $q->select(DB::raw('SUM(quantity)'))
                ->whereHas('order', fn($order) => $order->where('status', '!=', OrderStatus::Cancelled->value));
        }])
            ->having('total_sold', '>=', $minSold)
            ->orderByDesc('total_sold')
            ->take($limit);
    }


    // ==========================================
    // 2. Accessors (to get calculated values in Views)
    // ==========================================

    // The discount of this book that is active right now, or null
    public function getActiveDiscountAttribute()
    {
        // The discount was already loaded with()
        if ($this->relationLoaded('discount')) {
            $discount = $this->discount;

            $isActive = $discount && $discount->start_date <= now() && $discount->end_date >= now();

            return $isActive ? $discount : null;
        }

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
}
