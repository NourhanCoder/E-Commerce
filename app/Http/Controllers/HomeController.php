<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //1. Get 10 products with discounts if available
        $products = Product::withActiveDiscount()->take(10)->get();


        //2. Gets the 10 latest products with active discounts
        $latestProducts = Product::withActiveDiscount()->latest()->take(10)->get();

        //3. Gets the latest 4 books with discounts to avoid N+1 queries
        $sliderBooks = $latestProducts->take(4);

        //4. Current discounts for the timer(the books come with their active discount, to avoid one query per book)
        $discounts = Discount::with(['product' => fn($query) => $query->withActiveDiscount()])
            ->active()
            ->orderBy('end_date', 'asc')
            ->get();

        //5. Best-selling books
        $bestSellingProducts = Product::withActiveDiscount()
            ->bestSelling()
            ->get();

        return view('website.home', compact(
            'products',
            'sliderBooks',
            'discounts',
            'latestProducts',
            'bestSellingProducts'
        ));
    }


    /**
     * Show products from a specific category
     */
    public function productsByCategory(Category $category)
    {
        // Get products with their active discount
        $products = $category->products()
            ->withActiveDiscount()
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('website.products.index', compact('products', 'category'));
    }


    /**
     * Show details of a specific product
     */
    public function show(Product $product)
    {

        return view('website.products.show', compact('product'));
    }
}
