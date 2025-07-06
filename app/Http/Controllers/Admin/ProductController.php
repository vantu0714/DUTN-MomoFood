<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $statusFilter = $request->input('status');
            $query->where(function ($q) use ($statusFilter) {
                $q->where(function ($subQuery) use ($statusFilter) {
                    $subQuery->where('product_type', 'simple');
                    if ($statusFilter === 'Còn hàng') {
                        $subQuery->where('quantity_in_stock', '>', 0);
                    } elseif ($statusFilter === 'Hết hàng') {
                        $subQuery->where('quantity_in_stock', '=', 0);
                    }
                })->orWhere(function ($subQuery) use ($statusFilter) {
                    $subQuery->where('product_type', 'variant')->whereHas('variants', function ($variantQuery) use ($statusFilter) {
                        if ($statusFilter === 'Còn hàng') {
                            $variantQuery->where('quantity_in_stock', '>', 0);
                        } elseif ($statusFilter === 'Hết hàng') {
                            $variantQuery->where('quantity_in_stock', '=', 0);
                        }
                    });
                });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $availableProductsCount = Product::where(function ($query) {
            $query->where(function ($q) {
                $q->where('product_type', 'simple')->where('quantity_in_stock', '>', 0);
            })->orWhere(function ($q) {
                $q->where('product_type', 'variant')->whereHas('variants', function ($variantQuery) {
                    $variantQuery->where('quantity_in_stock', '>', 0);
                });
            });
        })->count();

        $outOfStockProductsCount = Product::where(function ($query) {
            $query->where(function ($q) {
                $q->where('product_type', 'simple')->where('quantity_in_stock', '=', 0);
            })->orWhere(function ($q) {
                $q->where('product_type', 'variant')->whereDoesntHave('variants', function ($variantQuery) {
                    $variantQuery->where('quantity_in_stock', '>', 0);
                });
            });
        })->count();

        $totalProducts = Product::count();

        $products = $query->paginate(10);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories', 'availableProductsCount', 'outOfStockProductsCount', 'totalProducts'));
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
            'original_price' => 'nullable|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|lte:original_price',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quantity_in_stock' => 'required_if:product_type,simple|nullable|integer|min:0',
            'product_type' => 'required|in:simple,variant',
        ], [
            'discounted_price.lte' => 'Giá khuyến mãi không được lớn hơn giá gốc.',
        ]);

        if (!array_key_exists('discounted_price', $validated)) {
            $validated['discounted_price'] = null;
        }

        // Nếu là sản phẩm có biến thể
        if ($validated['product_type'] === 'variant') {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products/temp', 'public');
                $validated['image'] = $imagePath;
            }

            // Chỉ lưu vào session
            Session::put('pending_product', $validated);

            return redirect()->route('admin.product_variants.create')
                ->with('success', 'Tiếp tục thêm biến thể cho sản phẩm.');
        }

        // Nếu là sản phẩm đơn → xử lý ảnh chính thức
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['status'] = isset($validated['quantity_in_stock']) && $validated['quantity_in_stock'] > 0 ? 1 : 0;

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm thành công.');
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
            'description' => 'nullable|string',
            'original_price' => 'nullable|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|max:100', // phần trăm giảm
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quantity_in_stock' => 'required_if:product_type,simple|nullable|integer|min:0',
        ]);

        $validated['status'] = isset($validated['quantity_in_stock']) && $validated['quantity_in_stock'] > 0 ? 1 : 0;

        if (!empty($validated['discounted_price']) && !empty($validated['original_price'])) {
            $percent = $validated['discounted_price'];
            $validated['discounted_price'] = round($validated['original_price'] * (1 - $percent / 100), 0);
        } else {
            $validated['discounted_price'] = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công');
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $actionType = $request->input('action_type');

        $hasOrders = $product->orderDetails()->exists() ||
            $product->variants()->whereHas('orderDetails')->exists();

        if ($hasOrders) {
            return redirect()->route('admin.products.index')->with('error', 'Không thể xóa sản phẩm vì đã có đơn hàng liên quan.');
        }

        if ($actionType === 'variants') {
            $product->variants()->delete();
            return redirect()->route('admin.products.index')->with('success', 'Đã xóa các biến thể của sản phẩm.');
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->variants()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm và các biến thể.');
    }

    public function showVariants($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return view('products.variants', compact('product'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'variants.attributeValues.attribute'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }
}
