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
        $query = Product::with('category')->orderBy('created_at', 'desc');
        // Tìm kiếm theo tên sản phẩm
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }


        // Lọc theo trạng thái sản phẩm
        if ($request->filled('status')) {
            $statusFilter = $request->input('status');
            $query->where(function ($q) use ($statusFilter) {
                $q->where(function ($subQuery) use ($statusFilter) {
                    // Sản phẩm đơn
                    $subQuery->where('product_type', 'simple');

                    if ($statusFilter === 'Còn hàng') {
                        $subQuery->where('quantity', '>', 0);
                    } elseif ($statusFilter === 'Hết hàng') {
                        $subQuery->where('quantity', '=', 0);
                    }
                })->orWhere(function ($subQuery) use ($statusFilter) {
                    // Sản phẩm có biến thể
                    $subQuery->where('product_type', 'variant')->whereHas('variants', function ($variantQuery) use ($statusFilter) {
                        if ($statusFilter === 'Còn hàng') {
                            $variantQuery->where('quantity', '>', 0);
                        } elseif ($statusFilter === 'Hết hàng') {
                            $variantQuery->where('quantity', '=', 0);
                        }
                    });
                });
            });
        }


        // Lọc theo danh mục (nếu có)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }


        $availableProductsCount = Product::where(function ($query) {
            $query->where(function ($q) {
                $q->where('product_type', 'simple')->where('quantity', '>', 0);
            })->orWhere(function ($q) {
                $q->where('product_type', 'variant')->whereHas('variants', function ($variantQuery) {
                    $variantQuery->where('quantity', '>', 0);
                });
            });
        })->count();

        $outOfStockProductsCount = Product::where(function ($query) {
            $query->where(function ($q) {
                $q->where('product_type', 'simple')->where('quantity', '=', 0);
            })->orWhere(function ($q) {
                $q->where('product_type', 'variant')->whereDoesntHave('variants', function ($variantQuery) {
                    $variantQuery->where('quantity', '>', 0);
                });
            });
        })->count();


        // Lấy sản phẩm phân trang
        // $products = Product::with('category')->paginate(10);

        // Đếm tổng số sản phẩm (kể cả hết hàng)
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
            'quantity' => 'required_if:product_type,simple|nullable|integer|min:0',
            'product_type' => 'required|in:simple,variant',
        ], [
            'discounted_price.lte' => 'Giá khuyến mãi không được lớn hơn giá gốc.',
        ]);

        if (!array_key_exists('discounted_price', $validated)) {
            $validated['discounted_price'] = null;
        }


        // ✅ Gán status theo loại sản phẩm
        if ($validated['product_type'] === 'simple') {
            $validated['status'] = isset($validated['quantity']) && $validated['quantity'] > 0 ? 1 : 0;
        } else {
            $validated['status'] = 0;
        }

        // ✅ Xử lý upload ảnh nếu có
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // ✅ Tạo sản phẩm
        $product = Product::create($validated);

        // ✅ Điều hướng sau khi tạo xong
        if ($validated['product_type'] === 'variant') {
            return redirect()->route('admin.product_variants.create', ['product_id' => $product->id])
                ->with('success', 'Thêm sản phẩm thành công. Bây giờ hãy thêm các biến thể.');
        }

        return redirect()->route('products.index')->with('success', 'Sản phẩm không biến thể đã được thêm thành công.');
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
            'discounted_price' => 'nullable|numeric|min:0|max:100', // đây là % giảm
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);

        // Cập nhật trạng thái
        $validated['status'] = $validated['quantity'] > 0 ? 1 : 0;

        // Tính giá khuyến mãi từ phần trăm giảm giá
        if (!empty($validated['discounted_price']) && !empty($validated['original_price'])) {
            $percent = $validated['discounted_price'];
            $validated['discounted_price'] = round($validated['original_price'] * (1 - $percent / 100), 0);
        } else {
            $validated['discounted_price'] = null;
        }

        // Xử lý ảnh
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

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Kiểm tra nếu sản phẩm có đơn hàng (qua orderDetails hoặc variants)
        $hasOrders = $product->orderDetails()->exists() ||
            $product->variants()->whereHas('orderDetails')->exists();

        if ($hasOrders) {
            return redirect()->route('products.index')->with('error', 'Không thể xóa sản phẩm vì đã có đơn hàng liên quan.');
        }

        // Nếu không có đơn hàng, thì xóa
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công.');
    }


    public function showVariants($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return view('products.variants', compact('product'));
    }
    public function show($id)
    {
        $product = Product::with([
            'category',
            'variants.attributeValues.attribute'
        ])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }
}
