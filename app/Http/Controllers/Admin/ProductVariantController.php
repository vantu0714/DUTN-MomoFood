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


class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'attributeValues.attribute'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                    ->orWhere('product_code', 'LIKE', "%{$search}%");
            })->orWhere('sku', 'LIKE', "%{$search}%");
        }

        $variantsPaginated = $query->paginate(10)->withQueryString();
        $groupedVariants = $variantsPaginated->getCollection()->groupBy('product_id');

        return view('admin.product_variants.index', [
            'variantsPaginated' => $variantsPaginated,
            'groupedVariants' => $groupedVariants,
            'search' => $request->input('search'),
        ]);
    }


    public function create(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);
        $attributes = Attribute::with('values')->get();

        // Lấy danh sách size từ bảng AttributeValue
        $sizeValues = Attribute::where('name', 'Size')->first()?->values ?? collect();

        return view('admin.product_variants.create', compact('product', 'sizeValues'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);
            $originalPrice = $product->original_price;
            $productCode = $product->product_code;

            foreach ($request->variants as $variantData) {
                $mainAttr = $variantData['main_attribute'] ?? null;
                $subAttrs = $variantData['sub_attributes'] ?? [];
                $imagePath = null;

                if (isset($variantData['image']) && $variantData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $variantData['image']->store('variants', 'public');
                }

                if ($mainAttr && !empty($mainAttr['value'])) {
                    $attribute = Attribute::firstOrCreate(['name' => $mainAttr['name']]);
                    $mainAttributeValue = AttributeValue::firstOrCreate([
                        'attribute_id' => $attribute->id,
                        'value' => $mainAttr['value'],
                    ]);
                } else {
                    $mainAttributeValue = null;
                }

                foreach ($request->variants as $variantData) {
                    $mainAttr = $variantData['main_attribute'] ?? null;
                    $subAttrs = $variantData['sub_attributes'] ?? [];

                    if ($mainAttr && !empty($mainAttr['value'])) {
                        $attribute = Attribute::firstOrCreate(['name' => $mainAttr['name']]);
                        $mainAttributeValue = AttributeValue::firstOrCreate([
                            'attribute_id' => $attribute->id,
                            'value' => $mainAttr['value'],
                        ]);
                    } else {
                        $mainAttributeValue = null;
                    }

                    foreach ($subAttrs as $subAttr) {
                        if ($subAttr['price'] < $originalPrice) {
                            return back()->withInput()->with('error', 'Giá biến thể không được thấp hơn giá gốc của sản phẩm (' . number_format($originalPrice) . '₫)');
                        }

                        // Tạo SKU cho mỗi lựa chọn
                        $sku = $productCode;
                        if ($mainAttributeValue) {
                            $sku .= '-' . strtoupper(Str::slug($mainAttributeValue->value));
                        }

                        // Xử lý ảnh riêng cho từng lựa chọn
                        $imagePath = null;
                        if (isset($subAttr['image']) && $subAttr['image'] instanceof \Illuminate\Http\UploadedFile) {
                            $imagePath = $subAttr['image']->store('variants', 'public');
                        }

                        $variant = ProductVariant::create([
                            'product_id' => $request->product_id,
                            'price' => $subAttr['price'],
                            'quantity_in_stock' => $subAttr['quantity_in_stock'],
                            'sku' => $sku,
                            'image' => $imagePath,
                        ]);

                        if ($mainAttributeValue) {
                            ProductVariantValue::create([
                                'product_variant_id' => $variant->id,
                                'attribute_value_id' => $mainAttributeValue->id,
                                'price_adjustment' => 0,
                            ]);
                        }

                        if (!empty($subAttr['attribute_value_id'])) {
                            ProductVariantValue::create([
                                'product_variant_id' => $variant->id,
                                'attribute_value_id' => $subAttr['attribute_value_id'],
                                'price_adjustment' => 0,
                            ]);
                        }
                    }
                }
            }

            $this->updateProductStatus($request->product_id);
            DB::commit();

            return redirect()->route('admin.product_variants.index')->with('success', 'Tạo biến thể thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
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
}
