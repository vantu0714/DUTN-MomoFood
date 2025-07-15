<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOrigin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use League\CommonMark\Extension\Attributes\Node\Attributes;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants'])
            ->withCount([
                'variants as min_price' => function ($q) {
                    $q->select(DB::raw('MIN(price)'));
                },
                'variants as max_price' => function ($q) {
                    $q->select(DB::raw('MAX(price)'));
                },
            ])
            ->orderBy('created_at', 'desc');


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
    $origins = ProductOrigin::all();

    return view('admin.products.create', compact('categories', 'origins'));
}

    public function store(Request $request)
    {
        $rules = [
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'category_id' => 'required|exists:categories,id',
            'product_type' => 'required|in:simple,variant',
            'original_price' => 'nullable|numeric|min:1',
            'discounted_price' => 'nullable|numeric|lt:original_price|gt:0',
            'quantity_in_stock' => 'exclude_if:product_type,variant|required|integer|min:1',
            'expiration_date' => 'nullable|date|after:today',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'origin_id' => 'required|exists:product_origins,id',
        ];


        if ($request->input('product_type') === 'simple') {
            $rules['original_price'] = ['required', 'numeric', 'gt:0'];
            $rules['discounted_price'] = ['nullable', 'numeric', 'gt:0'];
            $rules['quantity_in_stock'] = ['required', 'integer', 'min:1'];
        }

        $messages = [
            'product_name.required' => 'Tên sản phẩm là bắt buộc.',
            'product_code.required' => 'Mã sản phẩm là bắt buộc.',
            'product_code.unique' => 'Mã sản phẩm đã tồn tại.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'product_type.required' => 'Vui lòng chọn loại sản phẩm.',
            'original_price.gt' => 'Giá gốc phải lớn hơn 0.',
            'discounted_price.gt' => 'Giá khuyến mãi phải lớn hơn 0.',
            'discounted_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'quantity_in_stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'quantity_in_stock.min' => 'Số lượng tồn kho phải lớn hơn 0.',
            'expiration_date.after' => 'Ngày hết hạn phải sau hôm nay.',
            'origin_id.required' => 'Vui lòng chọn xuất xứ.',
            'origin_id.exists' => 'Xuất xứ không hợp lệ.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // Rule bổ sung: discounted_price phải < original_price (nếu có)
        $validator->after(function ($validator) use ($request) {
            if (
                $request->input('product_type') === 'simple' &&
                $request->filled('discounted_price') &&
                floatval($request->input('discounted_price')) >= floatval($request->input('original_price'))
            ) {
                $validator->errors()->add('discounted_price', 'Giá khuyến mãi phải nhỏ hơn giá gốc.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        // Nếu sản phẩm có biến thể
        if ($validated['product_type'] === 'variant') {
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products/temp', 'public');
            }

            unset($validated['original_price'], $validated['discounted_price'], $validated['quantity_in_stock']);

            Session::put('pending_product', $validated);
            return redirect()->route('admin.product_variants.create')
                ->with('success', 'Tiếp tục thêm biến thể cho sản phẩm.');
        }

        // Sản phẩm đơn
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Nếu không có giá khuyến mãi hoặc không hợp lệ
        if (
            !$request->filled('discounted_price') ||
            floatval($request->input('discounted_price')) <= 0 ||
            floatval($request->input('discounted_price')) >= floatval($request->input('original_price'))
        ) {
            $validated['discounted_price'] = null;
        }

        $validated['status'] = $validated['quantity_in_stock'] > 0 ? 1 : 0;

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm thành công.');
    }
   public function edit($id)
{
    $product = Product::with('variants.attributeValues.attribute', 'origin')->findOrFail($id);
    $categories = Category::all();
    $attributes = Attribute::with('values')->get();
    $origins = ProductOrigin::all(); 
    // Kiểm tra sản phẩm có biến thể không
    $hasVariants = $product->variants && $product->variants->isNotEmpty();

    return view('admin.products.edit', compact('product', 'categories', 'attributes', 'hasVariants', 'origins'));
}


    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $product->id,
            'category_id' => 'required|exists:categories,id',
             'origin_id' => 'required|exists:product_origins,id', 
            'description' => 'nullable|string',
            'original_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:99.99',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quantity_in_stock' => 'nullable|integer|min:0', // bỏ required_if nếu có biến thể
        ]);

        // Tính discounted_price
        if (!empty($validated['original_price']) && !empty($validated['discount_percent'])) {
            $percent = $validated['discount_percent'];
            $discountAmount = $validated['original_price'] * ($percent / 100);
            $validated['discounted_price'] = round($validated['original_price'] - $discountAmount, 0);
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

        unset($validated['discount_percent']);

        // Cập nhật sản phẩm lần đầu
        $product->update($validated);

        // Nếu có biến thể thì tính lại tồn kho
        if ($product->variants()->exists()) {
            $totalVariantQty = $product->variants()->sum('quantity_in_stock');
            $product->update(['quantity_in_stock' => $totalVariantQty]);
        }

        // Cập nhật trạng thái chính xác sau cùng
        $product->update([
            'status' => $product->quantity_in_stock > 0 ? 1 : 0
        ]);

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
    $product = Product::with([
        'category',
        'origin', // <--- Thêm dòng này
        'variants.attributeValues.attribute'
    ])->findOrFail($id);

    // Gọi riêng để đảm bảo lấy đúng thứ tự
    $product->setRelation(
        'variants',
        $product->variants()->orderBy('price', 'asc')->get()
    );

    return view('admin.products.show', compact('product'));
}

}
