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
        $sizeValues = Attribute::where('name', 'Size')->first()?->values ?? collect();

        return view('admin.product_variants.create', compact('product', 'attributes', 'sizeValues'));
    }





    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Lấy dữ liệu từ session nếu chưa có product_id
            $pendingProduct = Session::get('pending_product');

            if (!$request->filled('product_id') && !$pendingProduct) {
                return redirect()->route('admin.products.create')->with('error', 'Thông tin sản phẩm bị thiếu.');
            }

            // Tạo product nếu chưa tồn tại
            if (!$request->filled('product_id')) {
                $product = Product::create($pendingProduct);
                $productId = $product->id;
                Session::forget('pending_product');
            } else {
                $productId = $request->input('product_id');
                $product = Product::findOrFail($productId);
            }

            $variantsData = $request->input('variants', []);

            foreach ($variantsData as $variantIndex => $variant) {
                // Xử lý thuộc tính chính (ví dụ: Vị)
                $mainAttrName = trim($variant['main_attribute']['name']);
                $mainAttrValue = trim($variant['main_attribute']['value']);

                // Tạo hoặc tìm Attribute + AttributeValue cho thuộc tính chính
                $mainAttribute = Attribute::firstOrCreate(['name' => $mainAttrName]);
                $mainAttrVal = AttributeValue::firstOrCreate([
                    'attribute_id' => $mainAttribute->id,
                    'value' => $mainAttrValue
                ]);

                foreach ($variant['sub_attributes'] as $subIndex => $subAttr) {
                    $variantModel = new ProductVariant();
                    $variantModel->product_id = $productId;
                    $variantModel->price = $subAttr['price'];
                    $variantModel->quantity_in_stock = $subAttr['quantity_in_stock'] ?? 0;
                    $variantModel->sku = $subAttr['sku'] ?? null;

                    // Xử lý ảnh upload
                    $imageInputName = "variants.$variantIndex.sub_attributes.$subIndex.image";
                    $uploadedFile = $request->file($imageInputName);

                    if ($uploadedFile instanceof \Illuminate\Http\UploadedFile) {
                        $imagePath = $uploadedFile->store('product_variants', 'public');
                        $variantModel->image = $imagePath;
                    }

                    $variantModel->save();

                    // Gắn thuộc tính chính (Vị)
                    $variantModel->attributeValues()->attach($mainAttrVal->id);

                    // Gắn thuộc tính phụ (Size)
                    $sizeAttrValId = $subAttr['attribute_value_id'];
                    $variantModel->attributeValues()->attach($sizeAttrValId);
                }
            }

            // Cập nhật trạng thái sản phẩm (có thể là: active)
            $this->updateProductStatus($productId);

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm và các biến thể đã được thêm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
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
            $variant = ProductVariant::findOrFail($id);
            $product = Product::findOrFail($request->product_id);

            if ($request->price < $product->original_price) {
                return back()->withInput()->with('error', 'Giá biến thể không được thấp hơn giá gốc (' . number_format($product->original_price) . '₫)');
            }

            $imagePath = $variant->image;

            // Xử lý ảnh nếu có
            if ($request->hasFile('image')) {
                if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                    Storage::disk('public')->delete($variant->image);
                }

                $imagePath = $request->file('image')->store('variants', 'public');
            }

            $variant->update([
                'product_id' => $request->product_id,
                'price' => $request->price,
                'quantity_in_stock' => $request->quantity_in_stock,
                'sku' => $request->sku,
                'image' => $imagePath,
                'status' => $request->status ?? 1,
            ]);

            // Cập nhật lại attribute values
            ProductVariantValue::where('product_variant_id', $variant->id)->delete();

            if ($request->main_attribute_id) {
                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $request->main_attribute_id,
                    'price_adjustment' => 0,
                ]);
            }

            if ($request->sub_attribute_id) {
                ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $request->sub_attribute_id,
                    'price_adjustment' => 0,
                ]);
            }

            $this->updateProductStatus($request->product_id);

            DB::commit();
            return redirect()->route('admin.product_variants.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);

        if ($variant->orderDetails()->exists()) {
            return back()->with('error', 'Không thể xoá vì biến thể đã được sử dụng trong đơn hàng.');
        }

        $productId = $variant->product_id;

        ProductVariantValue::where('product_variant_id', $variant->id)->delete();

        if ($variant->image && Storage::disk('public')->exists($variant->image)) {
            Storage::disk('public')->delete($variant->image);
        }

        $variant->delete();

        $this->updateProductStatus($productId);

        return redirect()->route('admin.product_variants.index')->with('success', 'Đã xóa biến thể!');
    }
    /**
     * Cập nhật trạng thái "còn hàng / hết hàng" cho sản phẩm cha
     */
    protected function updateProductStatus($productId)
    {
        $product = Product::find($productId);

        if ($product) {
            $hasStock = $product->variants()->where('quantity_in_stock', '>', 0)->exists();
            $product->update(['status' => $hasStock ? 1 : 0]);
        }
    }
    // thêm biến thể cho sản phẩm có sẳn
    public function createMultiple()
    {
        $products = Product::where('status', 1)
            ->with(['variants.attributeValues.attribute'])
            ->orderByDesc('id') // sản phẩm mới nhất đầu tiên
            ->get();

        $sizeValues = Attribute::where('name', 'Size')->first()?->values ?? collect();

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

                foreach ($productData['variants'] ?? [] as $variantData) {
                    $mainAttr = $variantData['main_attribute'] ?? null;
                    $subAttrs = $variantData['sub_attributes'] ?? [];

                    // Tạo hoặc lấy giá trị thuộc tính chính
                    $mainAttributeValue = null;
                    if ($mainAttr && !empty($mainAttr['value'])) {
                        $attribute = Attribute::firstOrCreate(['name' => $mainAttr['name']]);
                        $mainAttributeValue = AttributeValue::firstOrCreate([
                            'attribute_id' => $attribute->id,
                            'value' => $mainAttr['value'],
                        ]);
                    }

                    foreach ($subAttrs as $subAttr) {
                        // Làm sạch giá (remove ".", ",", "₫" nếu có)
                        $rawPrice = $subAttr['price'] ?? '0';
                        $cleanPrice = (float) str_replace(['.', ',', '₫', ' '], '', $rawPrice);

                        if ($cleanPrice < $originalPrice) {
                            DB::rollBack();
                            return back()->withInput()->with('error', 'Giá biến thể không được thấp hơn giá gốc sản phẩm (' . number_format($originalPrice, 0, ',', '.') . '₫)');
                        }

                        // Check trùng biến thể (Vị + Size)
                        $exists = ProductVariant::where('product_id', $productId)
                            ->whereHas('attributeValues', function ($q) use ($mainAttributeValue) {
                                if ($mainAttributeValue) {
                                    $q->where('attribute_value_id', $mainAttributeValue->id);
                                }
                            })
                            ->whereHas('attributeValues', function ($q) use ($subAttr) {
                                if (!empty($subAttr['attribute_value_id'])) {
                                    $q->where('attribute_value_id', $subAttr['attribute_value_id']);
                                }
                            })
                            ->exists();

                        if ($exists) {
                            DB::rollBack();
                            return back()->withInput()->with('error', 'Biến thể với Vị "' . ($mainAttributeValue->value ?? '') . '" và Size đã tồn tại.');
                        }

                        // Upload ảnh nếu có
                        $imagePath = null;
                        if (isset($subAttr['image']) && $subAttr['image'] instanceof \Illuminate\Http\UploadedFile) {
                            $imagePath = $subAttr['image']->store('variants', 'public');
                        }

                        // Tạo SKU
                        $sku = $productCode;
                        if ($mainAttributeValue) {
                            $sku .= '-' . strtoupper(Str::slug($mainAttributeValue->value));
                        }
                        if (!empty($subAttr['attribute_value_id'])) {
                            $sizeValue = AttributeValue::find($subAttr['attribute_value_id']);
                            $sku .= '-' . strtoupper(Str::slug($sizeValue->value ?? ''));
                        }

                        // Tạo biến thể
                        $variant = ProductVariant::create([
                            'product_id' => $productId,
                            'price' => $cleanPrice,
                            'quantity_in_stock' => $subAttr['quantity_in_stock'],
                            'sku' => $sku,
                            'image' => $imagePath,
                        ]);

                        // Gắn thuộc tính chính
                        if ($mainAttributeValue) {
                            ProductVariantValue::create([
                                'product_variant_id' => $variant->id,
                                'attribute_value_id' => $mainAttributeValue->id,
                                'price_adjustment' => 0,
                            ]);
                        }

                        // Gắn thuộc tính phụ (size)
                        if (!empty($subAttr['attribute_value_id'])) {
                            ProductVariantValue::create([
                                'product_variant_id' => $variant->id,
                                'attribute_value_id' => $subAttr['attribute_value_id'],
                                'price_adjustment' => 0,
                            ]);
                        }
                    }
                }

                $this->updateProductStatus($productId);
            }
            DB::commit();
            return redirect()->route('admin.product_variants.index')->with('success', 'Đã thêm biến thể cho nhiều sản phẩm!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi thêm biến thể: ' . $e->getMessage());
        }
    }
    public function cancel()
    {
        Session::forget('pending_product');
        return redirect()->route('admin.products.index')->with('info', 'Đã hủy tạo sản phẩm.');
    }
}
