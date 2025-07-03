<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discounts = Discount::with('product')->orderBy('id', 'DESC')->paginate(10);
        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('admin.discounts.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'product_id' => 'required|exists:products,id',
        'percentage' => 'required|numeric|min:1|max:90',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
      ]);

      Discount::create([
        'product_id' => $request->product_id,
        'percentage' => $request->percentage,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
      ]);

      return redirect()->route('admin.discounts.index')->with('success', 'Discount added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        return view('admin.discounts.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        $products = Product::all();
        return view('admin.discounts.edit', compact('discount', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discount $discount)
    {
        $request->validate([
        'product_id' => 'required|exists:products,id',
        'percentage' => 'required|numeric|min:1|max:100',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
      ]);

      $discount->update($request->only(['product_id', 'percentage', 'start_date', 'end_date']));

      return redirect()->route('admin.discounts.index')->with('success', 'Discount updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();

       return redirect()->route('admin.discounts.index')->with('success', 'Discount deleted successfully!');
    }
}
