<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $sliderBooks = Product::withActiveDiscount()->latest()->take(4)->get();

        //4. Current discounts for the timer
        $discounts = Discount::with('product') 
        ->active()
        ->orderBy('end_date', 'asc')
        ->get(); 

        //5. Best-selling books
        $bestSellingProducts = Product::withActiveDiscount()
        ->bestSelling(minSold: 3, limit: 10)
        ->get();
      
       return view('website.home', compact( 
        'products', 
        'sliderBooks', 
        'discounts', 
        'latestProducts',
        'bestSellingProducts'
        ));
       
        // جلب التصنيفات الرئيسية ومعاها التصنيفات الفرعية
        // $categories = Category::with('children')->whereNull('parent_id')->get();

        // لو حبيت اعرض الكتاب بينتمي ل انهي تصنيف
        // $products = Product::with('category')->latest()->take(10)->get();   
   
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
