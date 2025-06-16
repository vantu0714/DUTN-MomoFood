<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $promotions = Promotion::orderBy('id', 'desc')->paginate(10); // ✅ đúng

        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.promotions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'promotion_name' => 'required|string|max:255',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Phần trăm giảm tối đa là 100%.');
                    }
                    if ($request->discount_type === 'fixed' && $value < 1000) {
                        $fail('Giảm theo số tiền phải từ 1000đ trở lên.');
                    }
                },
            ],

            'max_discount_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        // Bước 2: nếu giảm theo tiền thì bỏ max_discount_value
        if ($validated['discount_type'] === 'fixed') {
            $validated['max_discount_value'] = null;
        }

        Promotion::create($validated);

        return redirect()->route('promotions.index')->with('success', 'Thêm mã giảm giá thành công!');
    }

    public function show(string $id)
    {
        //
        $promotion = Promotion::findOrFail($id);
        return view('admin.promotions.show', compact('promotion'));
    }

    public function edit(string $id)
    {
        //
        $promotion = Promotion::findOrFail($id);
        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $promotion = Promotion::findOrFail($id);

        $request->merge([
            'discount_type' => $promotion->discount_type,
        ]);
        $request->validate([
            'promotion_name' => 'required|string|max:255',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $promotion->update([
            'promotion_name' => $request->promotion_name,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount_value' => $request->max_discount_value,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'description' => $request->description,
        ]);

        return redirect()->route('promotions.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return redirect()->route('promotions.index')->with('success', 'Xoá mã giảm giá thành công!');
    }
}
