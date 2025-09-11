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
            $query->whereIn('status', [0, 1, 2]);
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

        $outOfStockProductsCount = Product::where(function ($q) {
            // Simple hết hàng
            $q->where(function ($sub) {
                $sub->where('product_type', 'simple')
                    ->where('quantity_in_stock', '=', 0);
            })
                // Variant hết hàng (không còn biến thể nào có hàng)
                ->orWhere(function ($sub) {
                    $sub->where('product_type', 'variant')
                        ->whereDoesntHave('variants', function ($v) {
                            $v->where('quantity_in_stock', '>', 0);
                        });
                });
        })->count();


        $totalStockQuantity = Product::where('product_type', 'simple')->sum('quantity_in_stock') +
            ProductVariant::sum('quantity_in_stock');

        //  Tổng số sản phẩm (product_id duy nhất)
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
            'totalProductsCount'
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
            'video' => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:20480',
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
            'video.mimetypes' => 'Định dạng video không hợp lệ. Chỉ chấp nhận MP4, MOV, AVI.',
            'video.max' => 'Kích thước video tối đa là 20MB.',
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

        // Trường hợp sản phẩm có biến thể
        if ($validated['product_type'] === 'variant') {
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products/temp', 'public');
            }

            if ($request->hasFile('video')) {
                $validated['video'] = $request->file('video')->store('products/videos/temp', 'public');
            }

            unset($validated['original_price'], $validated['discounted_price'], $validated['quantity_in_stock']);

            Session::put('pending_product', $validated);
            return redirect()->route('admin.product_variants.create')
                ->with('success', 'Tiếp tục thêm biến thể cho sản phẩm.');
        }

        // Trường hợp sản phẩm đơn giản
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
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

        // Kiểm tra sản phẩm có biến thể không
        $hasVariants = $product->variants && $product->variants->isNotEmpty();

        // Nếu có biến thể, lấy biến thể đầu tiên để dùng cho form (ví dụ SKU, khối lượng, ...)
        $variant = $hasVariants ? $product->variants->first() : null;

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'attributes',
            'hasVariants',
            'origins',
            'variant'
        ));
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
            'video' => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:20480', // ✅ Video
            'quantity_in_stock' => 'nullable|integer|min:0',
        ]);

        //  Tính giá sau giảm
        if (!empty($validated['original_price']) && !empty($validated['discount_percent'])) {
            $percent = $validated['discount_percent'];
            $discountAmount = $validated['original_price'] * ($percent / 100);
            $validated['discounted_price'] = round($validated['original_price'] - $discountAmount, 0);
        } else {
            $validated['discounted_price'] = null;
        }

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        //  Xử lý upload video
        if ($request->hasFile('video')) {
            if ($product->video && Storage::disk('public')->exists($product->video)) {
                Storage::disk('public')->delete($product->video);
            }
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        unset($validated['discount_percent']);

        //  Cập nhật dữ liệu cơ bản
        $product->update($validated);

        //  Nếu có biến thể thì tổng hợp lại quantity
        if ($product->variants()->exists()) {
            $totalVariantQty = $product->variants()->sum('quantity_in_stock');
            $product->update(['quantity_in_stock' => $totalVariantQty]);
        } else {
            $totalVariantQty = $product->quantity_in_stock;
        }

        //  Cập nhật trạng thái sản phẩm
        if ($product->status != 0) {
            $product->update(['status' => $totalVariantQty > 0 ? 1 : 2]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $actionType = $request->input('action_type');

        if ($actionType === 'hide') {
            $completedStatuses = [4, 6];

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

            $inCart =
                CartItem::where('product_id', $product->id)->exists()
                ||
                CartItem::whereIn('product_variant_id', $product->variants->pluck('id'))->exists();

            if ($inCart) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Không thể ẩn sản phẩm vì đang có trong giỏ hàng.');
            }

            //  KHÔNG gọi recalcStockAndStatus ở đây    
            $product->update(['status' => 0]);

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được ẩn.');
        }
        if ($actionType === 'show') {
            // Nếu sản phẩm có số lượng > 0 thì là còn hàng (1), ngược lại hết hàng (2)
            $newStatus = $product->quantity_in_stock > 0 ? 1 : 2;
            $product->update(['status' => $newStatus]);

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được hiển thị.');
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
        // Lấy sản phẩm kèm quan hệ category, origin và variants (kèm attribute values)
        $product = Product::with([
            'category',
            'origin',
            'variants.attributeValues.attribute'
        ])->findOrFail($id);

        // Lấy tất cả biến thể (kể cả bị ẩn), sắp xếp theo giá
        $variants = $product->variants()->orderBy('price', 'asc')->get();

        // Nếu sản phẩm có biến thể => tính tổng tồn kho từ biến thể đang active (status = 1)
        $totalStock = $variants->isNotEmpty()
            ? $product->variants()->where('status', 1)->sum('quantity_in_stock')
            : $product->quantity_in_stock; // Nếu không có biến thể thì lấy stock của sản phẩm đơn

        // Gắn lại danh sách variants vào product để Blade không query thêm
        $product->setRelation('variants', $variants);

        return view('admin.products.show', [
            'product' => $product,
            'totalStock' => $totalStock
        ]);
    }
}
