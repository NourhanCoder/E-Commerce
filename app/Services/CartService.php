<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function add(Product $product, int $quantity): void
    {
        if(Auth::check()){
            $user = Auth::user();
            $cartItem = $user->cartItems()->where('product_id', $product->id)->first();

            if($cartItem){
                $newQuantity = min($cartItem->quantity + $quantity, $product->stock);
                $cartItem->update(['quantity' => $newQuantity]);

            }else{
                $user->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);

            }
              return;
        }

        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])){
            $cart[$product->id]['quantity'] = min($cart[$product->id]['quantity'] + $quantity, $product->stock);

        }else{
            $cart[$product->id] = [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->discounted_price ?? $product->price,
                'image' => $product->image(),
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);
    }

    // Aggregate cart items for display and calculate total amount
    public function items(): array
    {
        if(Auth::check()){
            $cartItems = Auth::user()->cartItems()->with('product')->get();

            $cart = [];
            $total = 0;

            foreach($cartItems as $item){
                $product = $item->product;
                $price = $product->discounted_price ?? $product->price;

                $cart[$product->id] = [
                    'title' => $product->title,
                    'price' => $price,
                    'image' => $product->image(),
                    'quantity' => $item->quantity,
                ];

                $total += $price * $item->quantity;
            }

            return ['cart' => $cart, 'total' => $total];
        }

        $cart = session()->get('cart', []);
        $total = 0;

        foreach($cart as $item){
            $total += $item['price'] * $item['quantity'];
        }

        return ['cart' => $cart, 'total' => $total];
    }

    public function updateQuantity(Product $product, ?string $action): array
    {
        if(Auth::check()){
            $user = Auth::user();
            $item = $user->cartItems()->where('product_id', $product->id)->first();

            // Ensure product exists in user's cart
            if(!$item){
                return ['status' => 'not_found'];
            }

            if($action === 'increase'){
                if($item->quantity >= $product->stock){
                    return [
                        'success' => false,
                        'message' => 'الكمية المطلوبة غير متوفرة في المخزون',
                    ];
                }

                $item->quantity += 1;
                $item->save();
            }elseif($action === 'decrease'){
                $item->quantity = max(1, $item->quantity - 1);
                $item->save();
            }

            // Recalculate total cart amount
            $total = $user->cartItems->sum(
                fn($i) => ($i->product->discounted_price ?? $i->product->price) * $i->quantity
            );

            return [
                'success' => true,
                'newQuantity' => $item->quantity,
                'total' => $total,
            ];
        }

        $cart = session()->get('cart', []);

        if (!isset($cart[$product->id])) {
            return ['status' => 'not_found'];
        }

        if ($action === 'increase') {
            if ($cart[$product->id]['quantity'] >= $product->stock) {
                return [
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة في المخزون',
                ];
            }

            $cart[$product->id]['quantity'] += 1;
        } elseif ($action === 'decrease') {
            $cart[$product->id]['quantity'] = max(1, $cart[$product->id]['quantity'] - 1);
        }

        session()->put('cart', $cart);

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return [
            'success' => true,
            'newQuantity' => $cart[$product->id]['quantity'],
            'total' => $total,
        ];
    }

    public function remove(Product $product): void
    {
        if(Auth::check()){
            Auth::user()->cartItems()->where('product_id', $product->id)->delete();

            return;
        }

        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])){
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }
    }
}