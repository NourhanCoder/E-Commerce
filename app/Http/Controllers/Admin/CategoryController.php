<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')->with('parent')->
        orderBy('id', 'DESC')->paginate(5);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get(); // علشان نختار parent لو 
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',
     ]);

     Category::create([
        'name' => $request->name,
        'parent_id' => $request->parent_id,
     ]);

     return redirect()->route('admin.categories.index')->with('success', 'Category added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
          // نحمل العلاقة مع المنتجات + نعدهم
        $category->loadCount('products', 'parent');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // dd($category->parent_id);
        $categories = Category::where('id', '!=', $category->id)->get(); // علشان ما يختارش نفسه كـ parent
        return view('admin.categories.edit', compact('category', 'categories'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id, // علشان ما يكونش parent لنفسه
     ]);

     $category->update([
        'name' => $request->name,
        'parent_id' => $request->parent_id,
     ]);

    return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');

    }
}
