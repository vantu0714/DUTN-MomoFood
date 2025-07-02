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
        $promotions = Promotion::orderBy('id', 'desc')->paginate(10);

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
            'promotion_name'      => 'required|string|max:255',
            'discount_type'       => 'required|in:fixed,percent',
            'discount_value'      => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent') {
                        if ($value < 1 || $value > 100) {
                            $fail('Phần trăm giảm phải nằm trong khoảng 1-100%.');
                        }
                    }

                    if ($request->discount_type === 'fixed' && $value < 1000) {
                        $fail('Số tiền giảm tối thiểu là 1000đ.');
                    }
                },
            ],
            'max_discount_value'  => 'nullable|numeric|min:0',
            'usage_limit'         => 'nullable|integer|min:1',
            'start_date'          => 'required|date|after_or_equal:' . Carbon::now()->subMinute()->toDateTimeString(),
            'end_date'            => 'required|date|after:start_date',
            'description'         => 'nullable|string',
            'status'              => 'nullable|boolean',
            'min_total_spent'     => 'nullable|numeric|min:0',
            'vip_only'            => 'nullable|boolean',
        ]);

        if (empty($validated['usage_limit'])) {
            $validated['usage_limit'] = null;
        }

        if ($validated['discount_type'] === 'fixed') {
            $validated['max_discount_value'] = null;
        }
        
        $validated['min_total_spent'] = $request->min_total_spent ?? null;
        $validated['vip_only'] = $request->has('vip_only') ? 1 : 0;

        Promotion::create($validated);

        return redirect()->route('admin.promotions.index')->with('success', 'Thêm mã giảm giá thành công!');
    }

    public function show(string $id)
    {
        //
        $promotion = Promotion::findOrFail($id);
        $now = Carbon::now('Asia/Ho_Chi_Minh');



        $isActive = $promotion->status == 1
            && $now->gte($promotion->start_date)
            && $now->lte($promotion->end_date);
        return view('admin.promotions.show', compact('promotion', 'isActive'));
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
            'start_date' => 'required|date|after_or_equal:' . Carbon::today()->toDateString(),
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        if (empty($validated['usage_limit'])) {
            $validated['usage_limit'] = null;
        }

        $validated['status'] = $request->has('status') ? (bool) $request->status : false;

        $promotion->update([
            'promotion_name' => $request->promotion_name,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount_value' => $request->max_discount_value,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'description' => $request->description,
            'status'             => $request->status,
            'usage_limit'        => $request->usage_limit,
        ]);

        return redirect()->route('admin.promotions.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'Xoá mã giảm giá thành công!');
    }
}
