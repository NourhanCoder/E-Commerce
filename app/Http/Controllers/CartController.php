<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request, Product $product)
    {
        $productId = $product->id;

        if (Auth::check()) {
            $user = Auth::user();

            $user->cartItems()->updateOrCreate(
                ['product_id' => $productId],
                ['quantity' => DB::raw('quantity + 1')]
            );
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += 1;
            } else {
                $cart[$productId] = [
                    'id' => $productId,
                    'title' => $product->title,
                    'price' => $product->discounted_price ?? $product->price,
                    'image' => $product->image(), // تأكدي إن دي بترجع string
                    'quantity' => 1,
                ];
            }
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'تمت إضافة المنتج إلى السلة');
    }


    public function index()
    {
        if (Auth::check()) {
            $cartItems = Auth::user()->cartItems()->with('product')->get();

            $cart = [];
            $total = 0;
            foreach ($cartItems as $item) {
                $product = $item->product;
                $price = $product->discounted_price ?? $product->price;
                $cart[$product->id] = [
                    'title' => $product->title,
                    'price' => $price,
                    'image' => $product->image,
                    'quantity' => $item->quantity,
                ];
                $total += $price * $item->quantity;
            }
        } else {
            $cart = session()->get('cart', []);
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
        }

        return view('website.home', compact('cart', 'total'));
    }


    public function updateQnty(Request $request, Product $product)
    {
        $productId = $product->id;

        if (Auth::check()) {
            $user = Auth::user();
            $item = $user->cartItems()->where('product_id', $productId)->first();

            if (!$item) {
                return response()->json(['status' => 'not_found']);
            }

            if ($request->action === 'increase') {
                $item->quantity += 1;
            } elseif ($request->action === 'decrease') {
                $item->quantity = max(1, $item->quantity - 1);
            }

            $item->save();

            $total = $user->cartItems->sum(fn($i) => ($i->product->discounted_price ?? $i->product->price) * $i->quantity);

            return response()->json([
                'success' => true,
                'newQuantity' => $item->quantity,
                'total' => $total
            ]);
        } else {
            $cart = session()->get('cart', []);
            if (!isset($cart[$productId])) {
                return response()->json(['status' => 'not_found']);
            }

            if ($request->action === 'increase') {
                $cart[$productId]['quantity'] += 1;
            } elseif ($request->action === 'decrease') {
                $cart[$productId]['quantity'] = max(1, $cart[$productId]['quantity'] - 1);
            }

            session()->put('cart', $cart);

            $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            return response()->json([
                'success' => true,
                'newQuantity' => $cart[$productId]['quantity'],
                'total' => $total
            ]);
        }
    }


    public function destroy(Request $request, Product $product)
    {
        $productId = $product->id;

        if (Auth::check()) {
            Auth::user()->cartItems()->where('product_id', $productId)->delete();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
                session()->put('cart', $cart);
            }
        }

        return redirect()->back()->with('success', 'تم حذف المنتج من السلة.');
    }
}
