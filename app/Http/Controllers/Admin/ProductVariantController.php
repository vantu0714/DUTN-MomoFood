<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariantValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;


class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'attributeValues.attribute'])->orderByDesc('created_at');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($q2) use ($search) {
                    $q2->where('product_name', 'LIKE', "%{$search}%")
                        ->orWhere('product_code', 'LIKE', "%{$search}%");
                })->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Lọc tồn kho
        if ($request->filled('stock_filter')) {
            $stockFilter = $request->input('stock_filter');
            if ($stockFilter === 'low') {
                $query->where('quantity_in_stock', '<=', 5);
            } elseif ($stockFilter === 'medium') {
                $query->whereBetween('quantity_in_stock', [6, 20]);
            } elseif ($stockFilter === 'high') {
                $query->where('quantity_in_stock', '>', 20);
            }
        }

        $variantsPaginated = $query->paginate(10)->withQueryString();
        $groupedVariants = $variantsPaginated->getCollection()->groupBy('product_id');

        return view('admin.product_variants.index', [
            'variantsPaginated' => $variantsPaginated,
            'groupedVariants' => $groupedVariants,
            'search' => $request->input('search'),
            'stockFilter' => $request->input('stock_filter'),
        ]);
    }
    public function create(Request $request)
    {
        $productId = $request->input('product_id');
        $product = null;

        if ($productId) {
            $product = Product::findOrFail($productId);
        } elseif (Session::has('pending_product')) {
            $pendingData = Session::get('pending_product');
            $product = new Product($pendingData); // tạo instance tạm
        } else {
            return redirect()->route('admin.products.create')->with('error', 'Không tìm thấy thông tin sản phẩm.');
        }

        $attributes = Attribute::with('values')->get();
        $sizeValues = Attribute::where('name', 'Khối lượng')->first()?->values ?? collect();

        return view('admin.product_variants.create', compact('product', 'attributes', 'sizeValues'));
    }
    public function show(ProductVariant $product_variant)
    {
        $attributeValues = $product_variant->attributeValues()->with('attribute')->get();

        // Lấy 'Vị' (main)
        $mainAttr = $attributeValues->first(fn($av) => $av->attribute->name === 'Vị');

        // Lấy 'Size' (sub)
        $subAttr = $attributeValues->first(fn($av) => $av->attribute->name === 'Khối lượng' || $av->attribute->name === 'Size');

        return response()->json([
            'sku' => $product_variant->sku,
            'price' => (int) $product_variant->price,
            'quantity_in_stock' => $product_variant->quantity_in_stock,
            'image_url' => $product_variant->image ? asset('storage/' . $product_variant->image) : null,
            'product_code' => $product_variant->product->code,
            'main_attribute' => $mainAttr ? [
                'id' => $mainAttr->id,
                'name' => $mainAttr->value,
            ] : null,
            'sub_attribute' => $subAttr ? [
                'id' => $subAttr->id,
                'name' => $subAttr->value,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Thông báo lỗi tùy chỉnh
            $messages = [
                'variants.*.main_attribute.name.required' => 'Tên thuộc tính chính không được để trống.',
                'variants.*.main_attribute.value.required' => 'Giá trị thuộc tính chính không được để trống.',
                'variants.*.sub_attributes.*.attribute_value_id.required' => 'Vui lòng chọn khối lượng.',
                'variants.*.sub_attributes.*.attribute_value_id.exists' => 'Khối lượng không hợp lệ.',

                'variants.*.sub_attributes.*.price.required' => 'Giá không được để trống.',
                'variants.*.sub_attributes.*.price.numeric' => 'Giá phải là số.',
                'variants.*.sub_attributes.*.price.min' => 'Giá phải lớn hơn 0.',

                'variants.*.sub_attributes.*.quantity_in_stock.required' => 'Số lượng không được để trống.',
                'variants.*.sub_attributes.*.quantity_in_stock.integer' => 'Số lượng phải là số nguyên.',
                'variants.*.sub_attributes.*.quantity_in_stock.min' => 'Số lượng phải lớn hơn 0.',
            ];

            // Validate dữ liệu
            $request->validate([
                'variants.*.main_attribute.name' => 'required|string|max:255',
                'variants.*.main_attribute.value' => 'required|string|max:255',
                'variants.*.sub_attributes.*.attribute_value_id' => 'required|exists:attribute_values,id',
                'variants.*.sub_attributes.*.price' => 'required|numeric|min:0.01',
                'variants.*.sub_attributes.*.quantity_in_stock' => 'required|integer|min:1',
            ], $messages);

            // Lấy thông tin sản phẩm từ session nếu chưa có
            $pendingProduct = Session::get('pending_product');

            if (!$request->filled('product_id') && !$pendingProduct) {
                return redirect()->route('admin.products.create')->with('error', 'Thông tin sản phẩm bị thiếu.');
            }

            // Tạo sản phẩm nếu là lần đầu
            if (!$request->filled('product_id')) {
                $product = Product::create($pendingProduct);
                $productId = $product->id;
                Session::forget('pending_product');
            } else {
                $productId = $request->input('product_id');
                $product = Product::findOrFail($productId);
            }

            $allPrices = [];

            foreach ($request->input('variants', []) as $variantIndex => $variant) {
                $mainAttrName = trim($variant['main_attribute']['name']);
                $mainAttrValue = trim($variant['main_attribute']['value']);

                $mainAttribute = Attribute::firstOrCreate(['name' => $mainAttrName]);
                $mainAttrVal = AttributeValue::firstOrCreate([
                    'attribute_id' => $mainAttribute->id,
                    'value' => $mainAttrValue,
                ]);

                foreach ($variant['sub_attributes'] as $subIndex => $subAttr) {
                    $variantModel = new ProductVariant();
                    $variantModel->product_id = $productId;
                    $variantModel->price = (float) $subAttr['price'];
                    $variantModel->quantity_in_stock = (int) $subAttr['quantity_in_stock'];
                    $variantModel->sku = $subAttr['sku'] ?? null;

                    $allPrices[] = $variantModel->price;

                    // Ảnh biến thể
                    $imageInputName = "variants.$variantIndex.sub_attributes.$subIndex.image";
                    if ($request->hasFile($imageInputName)) {
                        $uploadedFile = $request->file($imageInputName);
                        $variantModel->image = $uploadedFile->store('product_variants', 'public');
                    }

                    $variantModel->save();

                    // Gắn giá trị thuộc tính chính và phụ
                    $variantModel->attributeValues()->attach($mainAttrVal->id);
                    $variantModel->attributeValues()->attach($subAttr['attribute_value_id']);
                }
            }

            // Cập nhật giá thấp nhất vào sản phẩm chính
            if (!empty($allPrices)) {
                $product->original_price = min($allPrices);
                $product->discounted_price = null;
                $product->save();
            }

            // Cập nhật trạng thái tồn kho
            $this->updateProductStockAndStatus($product);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm và các biến thể đã được thêm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }
    protected function updateProductStockAndStatus(Product $product)
    {
        // Tính tổng tồn kho chỉ cho biến thể đang hiển thị
        $totalStock = ProductVariant::where('product_id', $product->id)
            ->where('status', 1)
            ->sum('quantity_in_stock');

        // Kiểm tra xem có biến thể nào hiển thị không
        $hasVisibleVariant = ProductVariant::where('product_id', $product->id)
            ->where('status', 1)
            ->exists();

        // Nếu không còn biến thể nào hiển thị → ẩn sản phẩm
        if (!$hasVisibleVariant) {
            $product->update([
                'quantity_in_stock' => 0,
                'status' => 0 // ẩn
            ]);
            return;
        }

        // Nếu có biến thể hiển thị:
        $product->update([
            'quantity_in_stock' => $totalStock,
            'status' => $totalStock > 0 ? 1 : 2 // 1 = còn hàng, 2 = hết hàng
        ]);
    }

    public function edit($id)
    {
        $variant = ProductVariant::with('product', 'attributeValues')->findOrFail($id);
        $products = Product::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.product_variants.edit', compact('variant', 'products', 'attributes'));
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Lấy biến thể + sản phẩm cha
            $variant = ProductVariant::with('values.attribute')->findOrFail($id);
            $product = Product::findOrFail($variant->product_id);

            // Check giá không thấp hơn giá gốc
            if ($request->price < $product->original_price) {
                $msg = 'Giá biến thể không được thấp hơn giá gốc (' . number_format($product->original_price) . '₫)';
                return $request->ajax()
                    ? response()->json(['error' => $msg], 422)
                    : back()->withInput()->with('error', $msg);
            }

            // Lấy "Vị" và "Size" từ request
            $mainAttrName = trim($request->input('main_attribute_name'));
            $subAttrId = $request->input('sub_attribute_id');

            // Kiểm tra trùng biến thể
            $exists = ProductVariant::where('product_id', $product->id)
                ->where('id', '<>', $variant->id)
                ->whereHas('values', function ($q) use ($mainAttrName) {
                    $q->whereHas('attribute', function ($q2) {
                        $q2->where('name', 'Vị');
                    })->where('value', $mainAttrName);
                })
                ->whereHas('values', function ($q) use ($subAttrId) {
                    $q->where('attribute_value_id', $subAttrId);
                })
                ->exists();
            if ($exists) {
                $msg = 'Biến thể với Vị "' . $mainAttrName . '" Size đã tồn tại.';
                return $request->ajax()
                    ? response()->json(['error' => $msg], 422)
                    : back()->withInput()->with('error', $msg);
            }

            // Tự động tạo SKU nếu chưa có
            if (empty($request->sku)) {
                $mainAttrName = trim($request->input('main_attribute_name'));
                $sizeValue = AttributeValue::find($request->sub_attribute_id)?->value ?? '';
                $baseCode = $product->code;
                $slugify = fn($str) => strtoupper(preg_replace('/[^A-Za-z0-9]/', '-', $str));

                $request->merge([
                    'sku' => $baseCode
                        . ($mainAttrName ? '-' . $slugify($mainAttrName) : '')
                        . ($sizeValue ? '-' . $slugify($sizeValue) : '')
                ]);
            }

            // Xử lý ảnh
            $imagePath = $variant->image;
            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('variants', 'public');
            }

            // Cập nhật biến thể
            $updateData = [
                'price' => $request->price,
                'quantity_in_stock' => $request->quantity_in_stock,
                'sku' => $request->sku,
                'image' => $imagePath,
            ];

            if ($variant->status != 0) {
                $updateData['status'] = $request->status ?? 1;
            }

            $variant->update($updateData);

            // Lấy Size cũ trước khi xóa
            $variant->load('values.attribute');
            $oldSizeAttrId = $variant->values
                ->first(fn($v) => $v->attribute->name === 'Khối lượng')
                ?->id;

            // Xóa attribute cũ
            ProductVariantValue::where('product_variant_id', $variant->id)->delete();

            // Thêm lại thuộc tính "Vị"
            if ($mainAttrName) {
                $mainAttr = Attribute::firstOrCreate(['name' => 'Vị']);
                $mainAttrValue = AttributeValue::firstOrCreate([
                    'attribute_id' => $mainAttr->id,
                    'value' => $mainAttrName,
                ]);

                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $mainAttrValue->id,
                    'price_adjustment' => 0,
                ]);
            }

            // Thêm lại "Size/Khối lượng"
            $sizeAttrId = $request->filled('sub_attribute_id') ? $request->sub_attribute_id : $oldSizeAttrId;
            if ($sizeAttrId) {
                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $sizeAttrId,
                    'price_adjustment' => 0,
                ]);
            }

            //Cập nhật trạng thái và tồn kho của sản phẩm cha
            $this->updateProductStatus($variant->product_id);
            $product->refresh(); // lấy dữ liệu mới sau update

            DB::commit();

            return $request->ajax()
                ? response()->json([
                    'message' => 'Cập nhật thành công',
                    'product_status' => $product->status,
                    'quantity_in_stock' => $product->quantity_in_stock
                ])
                : redirect()->route('admin.product_variants.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $request->ajax()
                ? response()->json(['error' => $e->getMessage()], 500)
                : back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        $variant = ProductVariant::findOrFail($id);
        $actionType = $request->input('action_type');
        $product = $variant->product;

        if ($actionType === 'hide') {
            if ($variant->cartItems()->exists()) {
                return response()->json([
                    'error' => 'Không thể ẩn vì biến thể này còn trong giỏ hàng.'
                ], 422);
            }

            $existsInActiveOrders = $variant->orderDetails()
                ->whereHas('order', function ($query) {
                    $query->whereNotIn('status', [4, 6]); // 4, 6 = hoàn tất / hủy
                })
                ->exists();

            if ($existsInActiveOrders) {
                return response()->json([
                    'error' => 'Không thể ẩn vì biến thể này còn trong đơn hàng chưa hoàn thành.'
                ], 422);
            }

            $variant->update(['status' => 0]);
        } elseif ($actionType === 'show') {
            $variant->update(['status' => 1]);
        }

        // Chỉ cập nhật stock khi thành công
        if ($product) {
            $product->quantity_in_stock = $product->variants()->where('status', 1)->sum('quantity_in_stock');
            $product->save();
        }

        return response()->json([
            'message' => $actionType === 'hide'
                ? 'Đã ẩn biến thể thành công!'
                : 'Đã hiển thị biến thể thành công!'
        ]);
    }
    protected function updateProductStatus($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return;
        }

        if ($product->product_type === 'variant') {
            $hasStock = $product->variants()->where('quantity_in_stock', '>', 0)->exists();
            $product->status = $hasStock ? 1 : 0;
        } else {
            $product->status = $product->quantity_in_stock > 0 ? 1 : 0;
        }

        $product->save();
    }

    // thêm biến thể cho sản phẩm có sẳn
    public function createMultiple()
    {
        $products = Product::where('status', 1)
            ->with(['variants.attributeValues.attribute'])
            ->withCount('variants')
            ->orderByDesc('id')
            ->get();

        $sizeValues = Attribute::where('name', 'Khối lượng')->first()?->values ?? collect();

        return view('admin.product_variants.create-multiple', compact('products', 'sizeValues'));
    }

    public function storeMultiple(Request $request)
    {
        DB::beginTransaction();

        try {
            $productsData = $request->input('products', []);

            foreach ($productsData as $productId => $productData) {
                $product = Product::findOrFail($productId);
                $originalPrice = (float) $product->original_price;
                $productCode = $product->product_code;

                $createdFirstVariant = false;

                foreach ($productData['variants'] ?? [] as $variantIndex => $variantData) {
                    $mainAttr = $variantData['main_attribute'] ?? null;
                    $subAttrs = $variantData['sub_attributes'] ?? [];

                    if (empty($mainAttr['name']) || empty($mainAttr['value'])) {
                        continue;
                    }

                    // Tạo hoặc lấy thuộc tính chính (VD: Vị)
                    $mainAttrModel = Attribute::firstOrCreate(['name' => trim($mainAttr['name'])]);
                    $mainAttrValue = AttributeValue::firstOrCreate([
                        'attribute_id' => $mainAttrModel->id,
                        'value' => trim($mainAttr['value']),
                    ]);

                    foreach ($subAttrs as $subIndex => $subAttr) {
                        $sizeAttrValueId = $subAttr['attribute_value_id'] ?? null;
                        $rawPrice = $subAttr['price'] ?? '0';
                        $quantity = isset($subAttr['quantity_in_stock']) ? (int) $subAttr['quantity_in_stock'] : 0;

                        $cleanPrice = (float) str_replace(['.', ',', '₫', ' '], '', $rawPrice);

                        if (!$sizeAttrValueId || $cleanPrice <= 0 || $quantity <= 0) {
                            continue;
                        }

                        // Không cho phép giá thấp hơn giá gốc
                        if ($cleanPrice < $originalPrice) {
                            DB::rollBack();
                            return back()->withInput()->with('error', "Giá biến thể không được thấp hơn giá gốc (ID SP: {$productId})");
                        }

                        // Kiểm tra trùng biến thể (theo vị + khối lượng)
                        $exists = ProductVariant::where('product_id', $productId)
                            ->whereHas('attributeValues', function ($q) use ($mainAttrValue, $sizeAttrValueId) {
                                $q->whereIn('attribute_value_id', [$mainAttrValue->id, $sizeAttrValueId]);
                            })
                            ->with(['attributeValues' => function ($q) use ($mainAttrValue, $sizeAttrValueId) {
                                $q->whereIn('attribute_value_id', [$mainAttrValue->id, $sizeAttrValueId]);
                            }])
                            ->get()
                            ->filter(function ($variant) use ($mainAttrValue, $sizeAttrValueId) {
                                $ids = $variant->attributeValues->pluck('id')->toArray();
                                return in_array($mainAttrValue->id, $ids) && in_array($sizeAttrValueId, $ids);
                            })
                            ->isNotEmpty();


                        if ($exists) {
                            DB::rollBack();
                            return back()->withInput()->with('error', "Biến thể với vị '{$mainAttrValue->value}' và khối lượng đã tồn tại (ID SP: {$productId})");
                        }

                        // Upload ảnh nếu có
                        $imagePath = null;
                        $imageInput = "products.{$productId}.variants.{$variantIndex}.sub_attributes.{$subIndex}.image";
                        $uploadedImage = $request->file($imageInput);

                        if ($uploadedImage instanceof \Illuminate\Http\UploadedFile) {
                            $imagePath = $uploadedImage->store('variants', 'public');
                        }

                        // Tạo SKU
                        $sizeValue = AttributeValue::find($sizeAttrValueId);
                        $sku = strtoupper($productCode . '-' . Str::slug($mainAttrValue->value) . '-' . Str::slug($sizeValue?->value ?? ''));

                        // Tạo biến thể
                        $variant = ProductVariant::create([
                            'product_id' => $productId,
                            'price' => $cleanPrice,
                            'quantity_in_stock' => $quantity,
                            'sku' => $sku,
                            'image' => $imagePath,
                            'status' => 1,
                        ]);

                        // Gắn thuộc tính chính (Vị)
                        ProductVariantValue::create([
                            'product_variant_id' => $variant->id,
                            'attribute_value_id' => $mainAttrValue->id,
                            'price_adjustment' => 0,
                        ]);

                        // Gắn thuộc tính phụ (Khối lượng)
                        ProductVariantValue::create([
                            'product_variant_id' => $variant->id,
                            'attribute_value_id' => $sizeAttrValueId,
                            'price_adjustment' => 0,
                        ]);

                        // Nếu là sản phẩm đơn → chuyển sang có biến thể
                        if (!$createdFirstVariant && $product->product_type === 'simple') {
                            $product->original_price = null;
                            $product->product_type = 'variant';
                            $product->save();
                        }

                        $createdFirstVariant = true;
                    }
                }

                //  Cập nhật tổng số lượng từ các biến thể sau khi thêm xong

                if ($product->product_type === 'variant') {
                    $totalQuantity = ProductVariant::where('product_id', $product->id)->sum('quantity_in_stock');
                    $product->quantity_in_stock = $totalQuantity;
                    $product->save();
                }


                // Cập nhật trạng thái sản phẩm nếu có
                $this->updateProductStatus($productId);
            }

            DB::commit();
            return redirect()->route('admin.product_variants.index')->with('success', 'Thêm biến thể thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi khi thêm biến thể: ' . $e->getMessage());
        }
    }
    public function cancel()
    {
        Session::forget('pending_product');
        return redirect()->route('admin.products.index')->with('info', 'Đã hủy tạo sản phẩm.');
    }
}
