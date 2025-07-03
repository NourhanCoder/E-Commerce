<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Gate for admin access
        Gate::define('admin-control', function (User $user) {
            return $user->type === 'admin';
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        App::setLocale('ar'); //للغة
        Paginator::useBootstrap(); //  للشكل

        //علشان يبعت المتغير تلقائي في اي صفحة هتحتوي على ناف علشان منضطرش نكتبه في كل داله و نتفادى الايرورز
        View::composer('website.layouts.nav', function ($view) {
            $view->with('categories', Category::with('children')->whereNull('parent_id')->get());
        });

        // // لعرض محتوى و عدد السلة في كل الصفحات
        // View::composer('*', function ($view) {
        //     $cart = session()->get('cart', []);
        //     $cartCount = collect($cart)->sum('quantity');

        //     $view->with([
        //         'cart' => $cart,
        //         'cartCount' => $cartCount,
        //     ]);
        // });


        // لعرض محتوى و عدد السلة في كل الصفحات
        View::composer('*', function ($view) {
        // لو المستخدم مسجل دخول
        if (Auth::check()) {
            $user = Auth::user();
            $cartItems = $user->cartItems()->with('product')->get();

            $cart = [];
            $cartCount = 0;

            foreach ($cartItems as $item) {
                $product = $item->product;

                if (!$product) continue;

                $cart[$item->product_id] = [
                    'title' => $product->title,
                    'price' => $product->discounted_price ?? $product->price,
                    'image' => $product->image(),
                    'quantity' => $item->quantity,
                ];
                $cartCount += $item->quantity;
            }
            
            $view->with([
                'cart' => $cart,
                'cartCount' => $cartCount,
            ]);
        } else {
            // لو مش مسجل دخول، استخدم السيشن
            $cart = session()->get('cart', []);
            $cartCount = collect($cart)->sum('quantity');

            $view->with([
                'cart' => $cart,
                'cartCount' => $cartCount,
            ]);
        }
    });
    }
}
