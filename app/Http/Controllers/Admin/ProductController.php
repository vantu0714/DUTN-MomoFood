<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Lọc theo trạng thái sản phẩm
        if ($request->filled('status')) {
            $statusFilter = $request->input('status');

            if ($statusFilter === 'Còn hàng') {
                $query->where('quantity', '>', 0);
            } elseif ($statusFilter === 'Hết hàng') {
                $query->where('quantity', '=', 0);
            }
        }

        // Lọc theo danh mục (nếu có)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

    
        $availableProductsCount = Product::where('quantity', '>', 0)->count();
        $outOfStockProductsCount = Product::where('quantity', '=', 0)->count();



        $products = $query->paginate(10);

        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories', 'availableProductsCount', 'outOfStockProductsCount'));
    }
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'category_id' => 'required|exists:categories,id',
            'original_price' => 'nullable|numeric',
            'discounted_price' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);
        $validated['status'] = $validated['quantity'] > 0 ? 1 : 0;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // Tạo sản phẩm
        $product = Product::create($validated);

        // Lưu biến thể (nếu có)
        if ($request->has('variants')) {
            foreach ($request->input('variants') as $variantData) {
                // Validate dữ liệu biến thể
                if (
                    !empty($variantData['name']) &&
                    isset($variantData['price']) &&
                    isset($variantData['quantity_in_stock']) &&
                    isset($variantData['sku'])
                ) {
                    $product->variants()->create([
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'quantity_in_stock' => $variantData['quantity_in_stock'],
                        'sku' => $variantData['sku'],
                        'status' => $variantData['status'] ?? 1,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'original_price' => 'nullable|numeric',
            'discounted_price' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quantity' => 'required|integer|min:0',

        ]);
        $validated['status'] = $validated['quantity'] > 0 ? 1 : 0;

        if ($request->hasFile('image')) {
            // Xoá ảnh cũ (nếu có)
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công');
    }
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Xóa ảnh nếu có
            if ($product->image && Storage::exists('public/' . $product->image)) {
                Storage::delete('public/' . $product->image);
            }

            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Sản phẩm đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->route('products.index')
                ->with('error', 'Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage());
        }
    }
    public function showVariants($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return view('products.variants', compact('product'));
    }
}
