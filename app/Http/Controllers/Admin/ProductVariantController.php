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

class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with(['product', 'attributeValues.attribute'])->paginate(10);
        return view('admin.product_variants.index', compact('variants'));
    }


    public function create()
    {
        $products = Product::all();
        $attributes = Attribute::with('values')->get();
        return view('admin.product_variants.create', compact('products', 'attributes'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Tạo biến thể
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('variants', 'public');
            }

            $variant = ProductVariant::create([
                'product_id' => $request->product_id,
                'name' => $request->name,
                'price' => $request->price,
                'quantity_in_stock' => $request->quantity_in_stock,
                'sku' => $request->sku,
                'status' => $request->status,
                'image' => $imagePath,
            ]);



            // Gắn attribute values cho variant
            if ($request->attribute_values) {
                foreach ($request->attribute_values as $attribute_value_id) {
                    ProductVariantValue::create([
                        'product_variant_id' => $variant->id,
                        'attribute_value_id' => $attribute_value_id,
                    ]);
                }
            }

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
                $imagePath = $request->file('image')->store('variants', 'public');
            }

            $variant->update([
                'product_id' => $request->product_id,
                'name' => $request->name,
                'price' => $request->price,
                'quantity_in_stock' => $request->quantity_in_stock,
                'sku' => $request->sku,
                'status' => $request->status,
                'image' => $imagePath,
            ]);



            // Xóa attribute value cũ
            ProductVariantValue::where('product_variant_id', $variant->id)->delete();

            // Gắn attribute values mới
            if ($request->attribute_values) {
                foreach ($request->attribute_values as $attribute_value_id) {
                    ProductVariantValue::create([
                        'product_variant_id' => $variant->id,
                        'attribute_value_id' => $attribute_value_id,
                    ]);
                }
            }

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

        // Xóa các liên kết attribute values (nếu cần)
        ProductVariantValue::where('product_variant_id', $variant->id)->delete();

        $variant->delete();

        return redirect()->route('admin.product_variants.index')->with('success', 'Đã xóa biến thể!');
    }
}
