<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Services\CartService;
use Illuminate\View\View;

class NavComposer
{
    public function __construct(private CartService $cartService)
    {
        
    }

    public function compose(View $view): void
    {
        $cart = $this->cartService->items()['cart'];

        $view->with([
            'categories' => Category::with('children')->whereNull('parent_id')->get(),
            'cart' => $cart,
            'cartCount' => collect($cart)->sum('quantity'),
        ]);
    }
}