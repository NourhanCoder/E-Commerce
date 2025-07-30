<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class HomeController extends Controller
{
    
    public function index()
    {
        $products = Product::with(['discount' => function ($q) {
            $q->where('start_date', '<=', now())
              ->where('end_date', '>=', now());
        }])->take(10)->get();

        $latestProducts = Product::with(['discount' => function ($q) {
            $q->where('start_date', '<=', now())
              ->where('end_date', '>=', now());
        }])->latest()->take(10)->get();

        $sliderBooks = Product::orderBy('id', 'desc')->take(4)->get();

        $discounts = Discount::with('product')
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->orderBy('end_date', 'asc')
            ->get();

        $bestSellingProducts = Product::withCount([
            'orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }
        ])
        ->having('total_sold', '>', 3)
        ->orderByDesc('total_sold')
        ->take(10)
        ->get();

       return response()->json(ProductResource::collection($products), 200);
    }

    
    public function productsByCategory(Category $category)
    {
        $products = $category->products()
            ->with([
                'category',
                'discount' => function ($query) {
                    $query->where('start_date', '<=', now())
                          ->where('end_date', '>=', now());
                }
            ])
            ->latest()
            ->paginate(12);

        return response()->json([ new CategoryResource($category),ProductResource::collection($products)]);
    }

    public function show(Product $product)
    {
        return response()->json(new ProductResource($product),201);
    }
}
