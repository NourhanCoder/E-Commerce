<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
     use HasFactory;

    protected $fillable = ['product_id', 'percentage', 'start_date', 'end_date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //Scope for available Sales
    public function scopeActive(Builder $query)
    {
        return $query->where('start_date','<=', now())
                     ->where('end_date', '>=', now());
    }
}
