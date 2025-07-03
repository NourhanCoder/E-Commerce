<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->orderBy('id', 'DESC')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subcategories = Category::whereNotNull('parent_id')->with('parent')->get();
        return view('admin.products.create', compact('subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'title'       => 'required|string|max:255',
        'author'      => 'required|string|max:255',
        'description' => 'required|string',
        'price'       => 'required|numeric|min:0',
        'stock'       => 'required|integer|min:0',
        'sku'         => 'nullable|string|max:100',
        'category_id' => 'required|exists:categories,id',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif',
      ]);

       $imageName = null;
       if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('products', $imageName, 'public');
        }

        Product::create([
        'title'       => $request->title,
        'author'      => $request->author,
        'description' => $request->description,
        'price'       => $request->price,
        'stock'       => $request->stock,
        'sku'         => $request->sku,
        'category_id' => $request->category_id,
        'image'       => $imageName,
      ]);

       return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category.parent'); //بيجيب بيانات التصنيف و البارينت في نفس الاستعلام
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // بنجيب كل التصنيفات علشان نظهرها في select
        $categories = Category::with('parent')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
        'title'       => 'required|string|max:255',
        'author'      => 'required|string|max:255',
        'description' => 'nullable|string',
        'price'       => 'required|numeric|min:0',
        'stock'       => 'required|integer|min:0',
        'sku'         => 'nullable|string|max:100',
        'category_id' => 'required|exists:categories,id',
        'image'       => 'nullable|image|mimes:png,jpg,jpeg,webp,gif',
      ]);

      $product->update([
        'title'       => $request->title,
        'author'      => $request->author,
        'description' => $request->description,
        'price'       => $request->price,
        'stock'       => $request->stock,
        'sku'         => $request->sku,
        'category_id' => $request->category_id,
     ]);

     // التعامل مع الصورة (لو المستخدم رفع صورة جديدة)
     if ($request->hasFile('image')) {
        // حذف الصورة القديمة لو موجودة
        if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('products', $imageName, 'public');
        $product->update(['image' => $imageName]);
     }

    return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // حذف الصورة من storage لو موجودة
      if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
        Storage::disk('public')->delete('products/' . $product->image);
      }

       // حذف المنتج من قاعدة البيانات
      $product->delete();

      return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
