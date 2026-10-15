<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }
    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        $this->cart->add($product, $validated['quantity']);

        return redirect()->back()->with('success', 'تمت إضافة المنتج إلى السلة');

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

        return redirect()->back()->with('success', 'تم حذف المنتج من السلة');
    }
}
