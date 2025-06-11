<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $variants = ProductVariant::with('product')->paginate(10);
        return view('admin.product_variants.index', compact('variants'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.product_variants.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity_in_stock' => 'required|integer',
            'sku' => 'nullable',
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        // Xử lý ảnh nếu có
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('variants', 'public');
        }

        ProductVariant::create($validated);

        return redirect()->route('admin.product_variants.index')->with('success', 'Đã thêm biến thể');
    }

    public function edit(ProductVariant $product_variant)
    {
        $products = Product::all();
       return view('admin.product_variants.edit', compact('product_variant', 'products'));

    }

    public function update(Request $request, ProductVariant $product_variant)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'name' => 'required',
        'price' => 'required|numeric',
        'quantity_in_stock' => 'required|integer',
        'sku' => 'nullable',
        'status' => 'required|boolean',
        'image' => 'nullable|image|max:2048',
        'description' => 'nullable|string',
    ]);

    $product_variant->update($validated);

    return redirect()->route('admin.product_variants.index')->with('success', 'Đã cập nhật biến thể');
}


    public function destroy(ProductVariant $product_variant)
    {
        $product_variant->delete();
        return redirect()->back()->with('success', 'Đã xoá biến thể');
    }
}
