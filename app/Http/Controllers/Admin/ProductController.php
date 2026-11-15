<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Services\ImageStorage;

class ProductController extends Controller
{
    private const IMAGE_FOLDER = 'products';

    public function __construct(private ImageStorage $images)
    {
        //
    }
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
    public function store(ProductRequest $request)
    {
       $data = $request->safe()->except('image');

       if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), self::IMAGE_FOLDER);
        }

        Product::create($data);

       return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Load the category and its parent in the same query
        $product->load('category.parent'); 

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
         // All categories, to show them in the select
        $categories = Category::with('parent')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
       $data = $request->safe()->except('image');
       $oldImage = $product->image;

     if ($request->hasFile('image')) {
        $data['image'] = $this->images->store($request->file('image'), self::IMAGE_FOLDER);
     }

     $product->update($data);

      // Remove the old image only after the product was saved with the new one
      if ($request->hasFile('image')) {
        $this->images->delete($oldImage, self::IMAGE_FOLDER);
      }

     return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
      $product->delete();

      // Remove the image only after the product was deleted
      $this->images->delete($product->image, self::IMAGE_FOLDER);

      return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
