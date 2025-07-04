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
        //لعرض 10 منتجات مع الخصومات ان وجدت
        $products = Product::with(['discount' => function ($q) {
        $q->where('start_date', '<=', now())
        ->where('end_date', '>=', now());
       }])->take(10)->get();

        // لعرض احدث المنتجات مع الخصومات ان وجدت
         $latestProducts = Product::with(['discount' => function ($q) {
        $q->where('start_date', '<=', now())
        ->where('end_date', '>=', now());
        }])->latest()->take(10)->get();
    
        // جلب التصنيفات الرئيسية ومعاها التصنيفات الفرعية
        // $categories = Category::with('children')->whereNull('parent_id')->get();

        // لو حبيت اعرض الكتاب بينتمي ل انهي تصنيف
        // $products = Product::with('category')->latest()->take(10)->get();

        // احدث 4 كتب للسلايدر
        $sliderBooks = Product::orderBy('id', 'desc')->take(4)->get();

        // جلب كل الخصومات الحالية (اللي بدأت ولسه منتهتش) للتايمر
        $discounts = Discount::with('product') // جلب بيانات الكتاب المرتبط
         ->whereDate('start_date', '<=', Carbon::today())
        ->whereDate('end_date', '>=', Carbon::today())
        ->orderBy('end_date', 'asc')
        ->get(); 
       
        //لعرض الكتب الاكثر مبيعا
        $bestSellingProducts = Product::withCount('orderItems')
        ->orderByDesc('order_items_count')->take(10)->get();

       return view('website.home', compact( 'products', 'sliderBooks', 'discounts', 'latestProducts', 'bestSellingProducts'));
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
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // جلب الخصم الحالي للمنتج (إن وجد)
        //تم استخدام دالة اكسيسور من داخل الموديل لتحسين الاداء
     return view('website.products.show', compact('product'));
    }

}
