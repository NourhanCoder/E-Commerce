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

    // التحقق من صحة الكمية
    $validated = $request->validate([
        'quantity' => 'required|integer|min:1|max:' . $product->stock,
    ]);

    $quantity = $validated['quantity'];

    if (Auth::check()) {
        $user = Auth::user();

        $cartItem = $user->cartItems()->where('product_id', $productId)->first();

        if ($cartItem) {
            // اجمع الكمية الجديدة على القديمة بدون تخطي المخزون
            $newQuantity = min($cartItem->quantity + $quantity, $product->stock);
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $user->cartItems()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }
    } else {
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            // اجمع الكمية الجديدة على القديمة بدون تخطي المخزون
            $cart[$productId]['quantity'] = min($cart[$productId]['quantity'] + $quantity, $product->stock);
        } else {
            $cart[$productId] = [
                'id' => $productId,
                'title' => $product->title,
                'price' => $product->discounted_price ?? $product->price,
                'image' => $product->image(),
                'quantity' => $quantity,
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
            //  تحقق من المخزون قبل الزيادة
            if ($item->quantity < $product->stock) {
                $item->quantity += 1;
                $item->save();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة في المخزون.'
                ]);
            }
        } elseif ($request->action === 'decrease') {
            $item->quantity = max(1, $item->quantity - 1);
            $item->save();
        }

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
            if ($cart[$productId]['quantity'] < $product->stock) {
                $cart[$productId]['quantity'] += 1;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة في المخزون.'
                ]);
            }
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
