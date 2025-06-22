<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComboItem;
use App\Models\Product;
use App\Models\ProductVariant;

class ComboItemController extends Controller
{
    public function index()
    {
        $comboItems = ComboItem::with('combo', 'itemable')->paginate(10);
        return view('admin.combo_items.index', compact('comboItems'));
    }

    public function create()
    {
        // Lấy danh sách ID các sản phẩm đang là combo (combo_id trong combo_items)
        $comboIds = ComboItem::pluck('combo_id')->unique();

        // Các sản phẩm combo (cha)
        $combos = Product::whereIn('id', $comboIds)->get();

        // Các sản phẩm có thể được thêm vào combo (không phải combo cha)
        $products = Product::whereNotIn('id', $comboIds)->get();

        // Lấy tất cả biến thể sản phẩm (product variants)
        $variants = ProductVariant::all();

        return view('admin.combo_items.create', compact('combos', 'products', 'variants'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'combo_id' => 'nullable|exists:products,id',
            'new_combo_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.itemable_type' => 'required|in:product,variant',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $comboId = $validated['combo_id'];

        if (!$comboId && !empty($validated['new_combo_name'])) {
            $combo = Product::create([
                'product_name' => $validated['new_combo_name'],
                'product_code' => 'COMBO-' . strtoupper(uniqid()),
                'original_price' => 0,
                'discounted_price' => 0,
                'status' => true,
                'is_combo' => true,
                'category_id' => 1,
            ]);
            $comboId = $combo->id;
        }

        if (!$comboId) {
            return back()->withErrors(['combo_id' => 'Vui lòng chọn combo có sẵn hoặc nhập tên combo mới.'])->withInput();
        }

        foreach ($validated['items'] as $item) {
            $itemableType = $item['itemable_type'] === 'product'
                ? Product::class
                : ProductVariant::class;

            $itemableId = $item['itemable_type'] === 'product'
                ? $item['product_id']
                : $item['variant_id'];

            if ($itemableId) {
                ComboItem::create([
                    'combo_id' => $comboId,
                    'itemable_type' => $itemableType,
                    'itemable_id' => $itemableId,
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        return redirect()->route('admin.combo_items.index')->with('success', 'Đã thêm thành phần vào combo.');
    }




    // Xóa một thành phần trong combo
    public function destroyCombo($comboId)
    {
        $combo = Product::with(['comboItems'])->findOrFail($comboId);

        // Kiểm tra nếu combo có trong đơn hàng hoặc giỏ hàng
        $usedInOrder = $combo->orderDetails()->exists();
        $usedInCart = $combo->cartItems()->exists();

        if ($usedInOrder || $usedInCart) {
            return redirect()->back()->with('error', 'Không thể xoá combo vì đã tồn tại trong giỏ hàng hoặc đơn hàng.');
        }

        // Xoá các thành phần combo trước
        $combo->comboItems()->delete();

        // Xoá combo
        $combo->delete();

        return redirect()->route('admin.combo_items.index')->with('success', 'Đã xoá combo thành công.');
    }
}
