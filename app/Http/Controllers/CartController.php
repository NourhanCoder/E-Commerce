<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Http\Requests\Cart\AddToCartRequest;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }
    public function add(AddToCartRequest $request, Product $product)
    {
        $requested = $request->validated()['quantity'];
        $added = $this->cart->add($product, $requested);

        if($added === 0){
            return redirect()->back()->with('warning', __('cart.already_max', [
            'stock' => $product->stock,
            ]));
        }

        if($added < $requested){
             return redirect()->back()->with('warning', __('cart.added_partial', [
            'added' => $added,
            'stock' => $product->stock,
            ]));
        }
        
        return redirect()->back()->with('success', __('cart.added'));

    }


    public function index()
    {
        ['cart' => $cart, 'total' => $total] = $this->cart->items();

        return view('website.home', compact('cart', 'total'));
    }


    public function updateQnty(Request $request, Product $product)
    {
       return response()->json(
        $this->cart->updateQuantity($product, $request->action)
       );
    }


    public function destroy(Product $product)
    {
       $this->cart->remove($product);

        return redirect()->back()->with('success',  __('cart.removed'));
    }
}
