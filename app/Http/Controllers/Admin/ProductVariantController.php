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

class ProductVariantController extends Controller
{
    public function index()
    {
        // Lấy tất cả biến thể với sản phẩm và attribute values
        $variantsPaginated = ProductVariant::with(['product', 'attributeValues.attribute'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // Gom nhóm theo product_id
        $groupedVariants = $variantsPaginated->getCollection()->groupBy('product_id');

        return view('admin.product_variants.index', [
            'variantsPaginated' => $variantsPaginated, // Để dùng phân trang
            'groupedVariants' => $groupedVariants,     // Dữ liệu gộp theo sản phẩm
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

            foreach ($request->variants as $variantData) {
                $mainAttr = $variantData['main_attribute'] ?? null;
                $subAttrs = $variantData['sub_attributes'] ?? [];
                $imagePath = null;

                if (isset($variantData['image']) && $variantData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $variantData['image']->store('variants', 'public');
                }

                foreach ($subAttrs as $subAttr) {
                    // 🔴 Kiểm tra giá
                    if ($subAttr['price'] < $originalPrice) {
                        return back()->withInput()->with('error', 'Giá biến thể không được thấp hơn giá gốc của sản phẩm (' . number_format($originalPrice) . '₫)');
                    }

                    $variant = ProductVariant::create([
                        'product_id' => $request->product_id,
                        'price' => $subAttr['price'],
                        'quantity_in_stock' => $subAttr['quantity'],
                        'sku' => uniqid('SKU_'),
                        'image' => $imagePath,
                    ]);

                    // Tạo thuộc tính chính (vị)
                    if ($mainAttr && !empty($mainAttr['value'])) {
                        $attribute = Attribute::firstOrCreate(['name' => $mainAttr['name']]);
                        $value = AttributeValue::firstOrCreate([
                            'attribute_id' => $attribute->id,
                            'value' => $mainAttr['value'],
                        ]);

                        ProductVariantValue::create([
                            'product_variant_id' => $variant->id,
                            'attribute_value_id' => $value->id,
                            'price_adjustment' => 0,
                        ]);
                    }

                    // Gắn attribute_value_id của Size
                    if (!empty($subAttr['attribute_value_id'])) {
                        ProductVariantValue::create([
                            'product_variant_id' => $variant->id,
                            'attribute_value_id' => $subAttr['attribute_value_id'],
                            'price_adjustment' => 0,
                        ]);
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

            $imagePath = $variant->image;
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
            ]);

            // Xóa các attribute cũ
            ProductVariantValue::where('product_variant_id', $variant->id)->delete();

            // Lưu lại các attribute mới với giá điều chỉnh
            if ($request->attribute_values) {
                foreach ($request->attribute_values as $attr) {
                    ProductVariantValue::create([
                        'product_variant_id' => $variant->id,
                        'attribute_value_id' => $attr['id'],
                        'price_adjustment' => $attr['price_adjustment'] ?? 0,
                    ]);
                }
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
