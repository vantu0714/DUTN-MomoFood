<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductOrigin;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

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

        // --- 2. Tìm kiếm ---
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        // --- 3. Lọc theo trạng thái ---
        if ($request->filled('status')) {
            $status = $request->input('status');

            if ($status === 'Còn hàng') {
                $query->where('status', 1)->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('product_type', 'simple')
                            ->where('quantity_in_stock', '>', 0);
                    })->orWhere(function ($sub) {
                        $sub->where('product_type', 'variant')
                            ->whereHas('variants', function ($v) {
                                $v->where('quantity_in_stock', '>', 0);
                            });
                    });
                });
            } elseif ($status === 'Hết hàng') {
                $query->where('status', 1)->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('product_type', 'simple')
                            ->where('quantity_in_stock', '=', 0);
                    })->orWhere(function ($sub) {
                        $sub->where('product_type', 'variant')
                            ->whereDoesntHave('variants', function ($v) {
                                $v->where('quantity_in_stock', '>', 0);
                            });
                    });
                });
            } elseif ($status === 'Ẩn') {
                $query->where('status', 0);
            }
        } else {
            $query->whereIn('status', [0, 1]);
        }

        // --- 4. Lọc theo danh mục ---
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // --- 5. Thống kê số lượng ---
        $availableProductsCount = Product::where('status', 1)->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('product_type', 'simple')
                    ->where('quantity_in_stock', '>', 0);
            })->orWhere(function ($sub) {
                $sub->where('product_type', 'variant')
                    ->whereHas('variants', function ($v) {
                        $v->where('quantity_in_stock', '>', 0);
                    });
            });
        })->count();

        $outOfStockProductsCount = Product::where('status', 1)->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('product_type', 'simple')
                    ->where('quantity_in_stock', '=', 0);
            })->orWhere(function ($sub) {
                $sub->where('product_type', 'variant')
                    ->whereDoesntHave('variants', function ($v) {
                        $v->where('quantity_in_stock', '>', 0);
                    });
            });
        })->count();

        $totalStockQuantity = Product::where('product_type', 'simple')->sum('quantity_in_stock') +
            ProductVariant::sum('quantity_in_stock');

        // 👇 Tổng số sản phẩm (product_id duy nhất)
        $totalProductsCount = Product::count();

        // --- 6. Kết quả ---
        $products = $query->paginate(10);
        $categories = Category::all();
        $hiddenProductsCount = Product::where('status', 0)->count();

        return view('admin.products.index', compact(
            'products',
            'categories',
            'availableProductsCount',
            'outOfStockProductsCount',
            'totalStockQuantity',
            'hiddenProductsCount',
            'totalProductsCount' // 👈 truyền biến này ra view
        ));
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
            'origin_id.required' => 'Vui lòng chọn xuất xứ.',
            'origin_id.exists' => 'Xuất xứ không hợp lệ.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

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

        if ($validated['product_type'] === 'variant') {
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products/temp', 'public');
            }

            unset($validated['original_price'], $validated['discounted_price'], $validated['quantity_in_stock']);

            Session::put('pending_product', $validated);
            return redirect()->route('admin.product_variants.create')
                ->with('success', 'Tiếp tục thêm biến thể cho sản phẩm.');
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

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
            'quantity_in_stock' => 'nullable|integer|min:0',
        ]);

        if (!empty($validated['original_price']) && !empty($validated['discount_percent'])) {
            $percent = $validated['discount_percent'];
            $discountAmount = $validated['original_price'] * ($percent / 100);
            $validated['discounted_price'] = round($validated['original_price'] - $discountAmount, 0);
        } else {
            $validated['discounted_price'] = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        unset($validated['discount_percent']);

        $product->update($validated);

        if ($product->variants()->exists()) {
            $totalVariantQty = $product->variants()->sum('quantity_in_stock');
            $product->update(['quantity_in_stock' => $totalVariantQty]);
        }

        $product->update([
            'status' => $product->quantity_in_stock > 0 ? 1 : 0
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $actionType = $request->input('action_type');

        if ($actionType === 'hide') {
            // Trạng thái hoàn tất hoặc hủy
            $completedStatuses = [4, 6];

            // Kiểm tra đơn hàng chưa hoàn tất (cả sản phẩm chính và variants)
            $hasPendingOrders =
                $product->orderDetails()
                ->whereHas('order', fn($q) => $q->whereNotIn('status', $completedStatuses))
                ->exists()
                ||
                OrderDetail::whereIn('product_variant_id', $product->variants->pluck('id'))
                ->whereHas('order', fn($q) => $q->whereNotIn('status', $completedStatuses))
                ->exists();

            if ($hasPendingOrders) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Không thể ẩn sản phẩm vì đang có trong đơn hàng chưa hoàn tất.');
            }

            // Kiểm tra giỏ hàng
            $inCart =
                CartItem::where('product_id', $product->id)->exists()
                ||
                CartItem::whereIn('product_variant_id', $product->variants->pluck('id'))->exists();

            if ($inCart) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Không thể ẩn sản phẩm vì đang có trong giỏ hàng.');
            }
            if ($product) {
                $product->quantity_in_stock = $product->variants()
                    ->where('status', 1)
                    ->sum('quantity_in_stock');
                if ($product->quantity_in_stock > 0) {
                    $product->status = 1; 
                } else {
                    $product->status = 0; 
                }
                $product->save();
            }
        }

        if ($actionType === 'show') {
            $product->update(['status' => 1]); // hiện sản phẩm
            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được hiển thị.');
        }

        return redirect()->route('admin.products.index')
            ->with('error', 'Hành động không hợp lệ.');
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
            'origin',
            'variants.attributeValues.attribute'
        ])->findOrFail($id);

        $product->setRelation(
            'variants',
            $product->variants()->orderBy('price', 'asc')->get()
        );

        return view('admin.products.show', compact('product'));
    }
}
