<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ComboItem;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class ComboItemController extends Controller
{
    public function index()
    {
        $comboItems = ComboItem::with([
            'combo:id,product_name,original_price,discounted_price',
            'itemable'
        ])->get();

        $grouped = $comboItems->groupBy('combo_id');

        return view('admin.combo_items.index', compact('grouped'));
    }


    public function create()
    {
        $comboIds = ComboItem::pluck('combo_id')->unique();

        $combos = Product::whereIn('id', $comboIds)->get();

        // CHỈ lấy sản phẩm đơn, chưa là combo, còn hàng
        $products = Product::whereNotIn('id', $comboIds)
            ->where('quantity_in_stock', '>', 0)
            ->where('is_combo', false)
            ->get();

        // CHỈ lấy biến thể còn hàng
        $variants = ProductVariant::where('quantity_in_stock', '>', 0)->get();

        return view('admin.combo_items.create', compact('combos', 'products', 'variants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'new_combo_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.itemable_type' => 'required|in:product,variant',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'original_price' => 'required|numeric|min:0',
            'discounted_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Kiểm tra combo name đã tồn tại chưa
            $existingCombo = Product::where('product_name', $validated['new_combo_name'])->first();
            if ($existingCombo) {
                return back()->withErrors(['new_combo_name' => 'Combo với tên này đã tồn tại.'])->withInput();
            }

            // Tạo combo mới
            $combo = Product::create([
                'product_name' => $validated['new_combo_name'],
                'product_code' => 'COMBO-' . strtoupper(uniqid()),
                'original_price' => $validated['original_price'],
                'discounted_price' => $validated['discounted_price'],
                'quantity_in_stock' => 0,
                'status' => true,
                'is_combo' => true,
                'category_id' => 1,
            ]);

            $minQuantity = PHP_INT_MAX;

            foreach ($validated['items'] as $item) {
                $type = $item['itemable_type'];
                $itemableType = $type === 'product' ? Product::class : ProductVariant::class;
                $itemableId = $type === 'product' ? $item['product_id'] : $item['variant_id'];

                if (!$itemableId) continue;

                ComboItem::create([
                    'combo_id' => $combo->id,
                    'itemable_type' => $itemableType,
                    'itemable_id' => $itemableId,
                    'quantity' => $item['quantity'],
                ]);

                // Tính tồn kho thấp nhất dựa trên từng thành phần
                $model = $itemableType::find($itemableId);
                if ($model) {
                    $stock = $model->quantity_in_stock ?? 0;
                    $maxCombo = floor($stock / $item['quantity']);
                    $minQuantity = min($minQuantity, $maxCombo);
                }
            }

            $minQuantity = ($minQuantity === PHP_INT_MAX) ? 0 : $minQuantity;

            $combo->update([
                'quantity_in_stock' => $minQuantity,
                'original_price' => $validated['original_price'],
                'discounted_price' => $validated['discounted_price'],
            ]);

            DB::commit();
            return redirect()->route('admin.combo_items.index')->with('success', 'Đã tạo combo mới thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Đã xảy ra lỗi khi tạo combo.')->withInput();
        }
    }




    public function destroyCombo($comboId)
    {
        $combo = Product::with(['comboItems'])->findOrFail($comboId);

        // Kiểm tra nếu combo này đã có trong order hoặc cart
        $usedInOrder = OrderDetail::where('product_id', $comboId)->exists();
        $usedInCart = CartItem::where('product_id', $comboId)->exists();

        if ($usedInOrder || $usedInCart) {
            return redirect()->back()->with('error', 'Không thể xoá combo vì đã tồn tại trong giỏ hàng hoặc đơn hàng.');
        }

        $combo->comboItems()->delete();
        $combo->delete();

        return redirect()->route('admin.combo_items.index')->with('success', 'Đã xoá combo thành công.');
    }
}
