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
            'combo_id' => 'required|exists:products,id',
            'itemable_type' => 'required|in:product,variant',
            'itemable_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $typeMap = [
            'product' => \App\Models\Product::class,
            'variant' => \App\Models\ProductVariant::class,
        ];

        ComboItem::create([
            'combo_id' => $validated['combo_id'],
            'itemable_id' => $validated['itemable_id'],
            'itemable_type' => $typeMap[$validated['itemable_type']],
            'quantity' => $validated['quantity'],
        ]);

        return redirect()->route('admin.combo_items.index')->with('success', 'Thêm thành phần combo thành công!');
    }

    public function destroy($id)
    {
        $item = ComboItem::findOrFail($id);
        $item->delete();
        return redirect()->back()->with('success', 'Đã xoá thành phần combo');
    }
}
