<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOrigin;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use League\CommonMark\Extension\Attributes\Node\Attributes;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. Cập nhật trạng thái hết hạn sử dụng ---

        // Cho sản phẩm đơn
        Product::where('product_type', 'simple')
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', now()->addDays(5))
            ->where('status', '!=', 2)
            ->update(['status' => 2]);

        // Cho sản phẩm biến thể
        Product::where('product_type', 'variant')
            ->where('status', '!=', 2)
            ->with('variants')
            ->get()
            ->each(function ($product) {
                if ($product->variants->count() > 0) {
                    $allExpired = $product->variants->every(function ($variant) {
                        return $variant->expiration_date && $variant->expiration_date <= now()->addDays(5);
                    });

                    if ($allExpired) {
                        $product->update(['status' => 2]);
                    }
                }
            });

        // --- 2. Query sản phẩm ---
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

        // --- 3. Tìm kiếm ---
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        // --- 4. Lọc theo trạng thái ---
        if ($request->filled('status')) {
            $status = $request->input('status');

            if ($status === 'Hết hạn sử dụng') {
                $query->where('status', 2);
            } elseif ($status === 'Còn hàng') {
                $query->where('status', 1)->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('product_type', 'simple')
                            ->where('quantity_in_stock', '>', 0)
                            ->where(function ($q2) {
                                $q2->whereNull('expiration_date')
                                    ->orWhere('expiration_date', '>', now()->addDays(5));
                            });
                    })->orWhere(function ($sub) {
                        $sub->where('product_type', 'variant')
                            ->whereHas('variants', function ($v) {
                                $v->where('quantity_in_stock', '>', 0)
                                    ->where(function ($q2) {
                                        $q2->whereNull('expiration_date')
                                            ->orWhere('expiration_date', '>', now()->addDays(5));
                                    });
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
            }
        } else {
            $query->whereIn('status', [0, 1, 2]);
        }

        // --- 5. Lọc theo danh mục ---
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // --- 6. Thống kê số lượng ---
        // Còn hàng
        $availableProductsCount = Product::where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('product_type', 'simple')
                    ->where('quantity_in_stock', '>', 0)
                    ->where(function ($q2) {
                        $q2->whereNull('expiration_date')
                            ->orWhere('expiration_date', '>', now()->addDays(5));
                    });
            })->orWhere(function ($sub) {
                $sub->where('product_type', 'variant')
                    ->whereHas('variants', function ($v) {
                        $v->where('quantity_in_stock', '>', 0)
                            ->where(function ($q2) {
                                $q2->whereNull('expiration_date')
                                    ->orWhere('expiration_date', '>', now()->addDays(5));
                            });
                    });
            });
        })->count();


        // Hết hàng
        $outOfStockProductsCount = Product::where(function ($q) {
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


        // Hết hạn sử dụng
        $expiredProductsCount = Product::where(function ($q) {
            // Sản phẩm đơn hết hạn
            $q->where(function ($sub) {
                $sub->where('product_type', 'simple')
                    ->whereNotNull('expiration_date')
                    ->where('expiration_date', '<=', now()->addDays(5));
            })
                ->orWhere(function ($sub) {
                    // Sản phẩm biến thể có tất cả các biến thể hết hạn
                    $sub->where('product_type', 'variant')
                        ->whereHas('variants', function ($v) {
                            $v->whereNotNull('expiration_date')
                                ->where('expiration_date', '<=', now()->addDays(5));
                        })
                        ->whereDoesntHave('variants', function ($v) {
                            $v->whereNull('expiration_date')
                                ->orWhere('expiration_date', '>', now()->addDays(5));
                        });
                });
        })->count();


        // Tổng tồn kho
        $totalStockQuantity = Product::where('product_type', 'simple')->sum('quantity_in_stock') +
            ProductVariant::sum('quantity_in_stock');

        // --- 7. Kết quả ---
        $products = $query->paginate(10);
        $categories = Category::all();

        return view('admin.products.index', compact(
            'products',
            'categories',
            'availableProductsCount',
            'outOfStockProductsCount',
            'expiredProductsCount',
            'totalStockQuantity'
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

        // Tính ngày giới hạn: ngày cũ + 30 ngày
        $oldExpirationDate = $product->expiration_date ? \Carbon\Carbon::parse($product->expiration_date) : null;
        $minEditDate = $oldExpirationDate ? $oldExpirationDate->copy()->addDays(1) : null;
        // Validate
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
            'expiration_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($minEditDate, $oldExpirationDate) {
                    if ($minEditDate) {
                        $newDate = \Carbon\Carbon::parse($value);
                        // Nếu ngày mới khác ngày cũ và nhỏ hơn giới hạn
                        if (!$newDate->equalTo($oldExpirationDate) && $newDate->lt($minEditDate)) {
                            $fail('Bạn chỉ được phép gia hạn ngày hết hạn từ ' . $minEditDate->format('d/m/Y') . ' trở đi.');
                        }
                    }
                },
            ],
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

        // Cập nhật sản phẩm
        $product->update($validated);

        // Nếu có biến thể thì tính lại tồn kho
        if ($product->variants()->exists()) {
            $totalVariantQty = $product->variants()->sum('quantity_in_stock');
            $product->update(['quantity_in_stock' => $totalVariantQty]);
        }

        // Cập nhật trạng thái sản phẩm
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
