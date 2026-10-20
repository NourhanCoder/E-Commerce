<?php
namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function add(Product $product, int $quantity): int
    {
        if(Auth::check()){
            $user = Auth::user();
            $cartItem = $user->cartItems()->where('product_id', $product->id)->first();

            $current = $cartItem->quantity ?? 0;
            $newQuantity = min($current + $quantity, $product->stock);

            if($cartItem){
                $cartItem->update(['quantity' => $newQuantity]);

            }else{
                $user->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $newQuantity,
                ]);

            }
              return max(0, $newQuantity - $current);
        }

        $cart = session()->get('cart', []);

        $current = $cart[$product->id]['quantity'] ?? 0;
        $newQuantity = min($current + $quantity, $product->stock);

        if(isset($cart[$product->id])){
            $cart[$product->id]['quantity'] = $newQuantity;

        }else{
            $cart[$product->id] = [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->discounted_price ?? $product->price,
                'image' => $product->image(),
                'quantity' => $newQuantity,
            ];
        }

        session()->put('cart', $cart);
        return max(0, $newQuantity - $current);
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
                        'message' => __('cart.out_of_stock'),
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
                    'message' => __('cart.out_of_stock'),
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

    public function mergeGuestCart(User $user): void
    {
        $cart = session()->get('cart', []);

        foreach($cart as $productId => $item){
            $user->cartItems()->updateOrCreate(
                ['product_id' => $productId],
                ['quantity' => $item['quantity']]
            );
        }

        session()->forget('cart');
    }
}