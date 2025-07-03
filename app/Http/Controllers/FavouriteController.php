<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Favourite;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $favourites = $user->favourites()->with('product')->orderBy('id', 'DESC')->paginate(10);

        return view('website.favourites', compact('favourites'));
    }
    public function toggle(Product $product)
    {
        $user = auth()->user();

        // نبحث في جدول favourites نفسه وليس من العلاقة
        $favourite = Favourite::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($favourite) {
            // لو السطر موجود، نحذفه
            $favourite->delete();
            $status = 'removed';
        } else {
            // لو مش موجود، نضيفه
            Favourite::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            $status = 'added';
        }

        return response()->json([
            'status' => $status,
            'count' => Favourite::where('user_id', $user->id)->count(),
        ]);
    }
}
