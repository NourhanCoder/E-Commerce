<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //لعرض كل المنتجات 
        $products = Product::with('discount')->latest()->take(10)->get(); 

        //لعرض احدث المنتجات
         $latestProducts = Product::latest()->take(10)->get();
    
        // جلب التصنيفات الرئيسية ومعاها التصنيفات الفرعية
        $categories = Category::with('children')->whereNull('parent_id')->get();

        // جلب آخر 12 منتج
        $products = Product::with('category')->latest()->take(12)->get();

        // احدث 4 كتب للسلايدر
        $sliderBooks = Product::orderBy('id', 'desc')->take(4)->get();

        // جلب كل الخصومات الحالية (اللي بدأت ولسه منتهتش) للتايمر
        $discounts = Discount::with('product') // جلب بيانات الكتاب المرتبط
         ->whereDate('start_date', '<=', Carbon::today())
        ->whereDate('end_date', '>=', Carbon::today())
        ->orderBy('end_date', 'asc')
        ->get(); 
       

       return view('website.home', compact('categories', 'products', 'sliderBooks', 'discounts', 'latestProducts'));
    }

    public function productsByCategory(Category $category)
   {
       // نجيب المنتجات مع علاقتها بالخصم الساري
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

       return view('website.products.index', compact('products', 'category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // جلب الخصم الحالي للمنتج (إن وجد)
        $activeDiscount = $product->discount()
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->first();

     return view('website.products.show', compact('product', 'activeDiscount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
