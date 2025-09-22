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
    private function generatePromotionCode($length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
    }

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
        $validated = $request->validate(
            [
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
                            if ($request->discount_type === 'percent' && $value > 50) {
                                $fail('Không được giảm quá 50% nếu là phần trăm.');
                            }
                        }

                        if ($request->discount_type === 'fixed') {
                            if ($value < 1000) {
                                $fail('Số tiền giảm tối thiểu là 1000đ.');
                            }

                            if (
                                is_numeric($request->min_total_spent) &&
                                $value > $request->min_total_spent * 0.5
                            ) {
                                $fail('Số tiền giảm không được vượt quá 50% tổng đơn hàng tối thiểu.');
                            }
                        }
                    },
                ],

                'max_discount_value'  => 'nullable|numeric|min:0',
                'usage_limit'         => 'nullable|integer|min:1',
                'start_date'          => 'required|date|after_or_equal:' . Carbon::now()->subMinute()->toDateTimeString(),
                'end_date'            => 'required|date|after:start_date',
                'description'         => 'nullable|string',
                'status'              => 'nullable|boolean',
                'min_total_spent'     => 'required|numeric|min:1000',
                'vip_only'            => 'nullable|boolean',
            ],
            [
                'discount_value.numeric'      => 'Số tiền giảm phải là số hợp lệ.',
                'promotion_name.required'     => 'Vui lòng nhập tên chương trình khuyến mãi.',
                'discount_type.required'      => 'Vui lòng chọn loại giảm giá.',
                'discount_value.required'     => 'Vui lòng nhập giá trị giảm.',
                'discount_value.min'          => 'Giá trị giảm phải lớn hơn 0.',
                'max_discount_value.min'      => 'Giá trị giảm tối đa phải lớn hơn hoặc bằng 0.',
                'min_total_spent.required'    => 'Vui lòng nhập mức chi tiêu tối thiểu.',
                'min_total_spent.min'         => 'Chi tiêu tối thiểu phải lớn hơn 1.000đ.',
                'start_date.required'         => 'Vui lòng chọn ngày bắt đầu.',
                'start_date.after_or_equal'   => 'Ngày bắt đầu phải từ hôm nay trở đi.',
                'end_date.required'           => 'Vui lòng chọn ngày kết thúc.',
                'start_date.after_or_equal'   => 'Ngày bắt đầu phải sau thời điểm hiện tại (tối thiểu sau 2 phút).',
                'status.required'             => 'Vui lòng chọn trạng thái.',
                'usage_limit.integer'         => 'Giới hạn lượt sử dụng phải là số nguyên.',
                'usage_limit.min'             => 'Giới hạn lượt sử dụng phải lớn hơn 0.',
            ],
        );

        if (empty($validated['usage_limit'])) {
            $validated['usage_limit'] = null;
        }

        if ($validated['discount_type'] === 'fixed') {
            $validated['max_discount_value'] = null;
        }

        $validated['vip_only'] = $request->has('vip_only') ? 1 : 0;
        $validated['min_total_spent'] = $request->min_total_spent ?? null;

        // Tạo code random, đảm bảo không trùng
        do {
            $validated['code'] = $this->generatePromotionCode(8); // VD: GIAMX7Y2
        } while (Promotion::where('code', $validated['code'])->exists());


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

        $now = now();

        // Nếu đã hết hạn thì tự động cập nhật về 0 trong DB
        if ($promotion->end_date < $now && $promotion->status == 1) {
            $promotion->status = 0;
            $promotion->save();
        }
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

        $discountType = $request->discount_type;

        $validated = $request->validate(
            [
                'promotion_name'      => 'required|string|max:255',
                'code'                => 'required|string|max:50|unique:promotions,code,' . $promotion->id,
                'discount_type'       => 'required|in:fixed,percent',
                // tùy theo discount_type mà thay đổi rule
                'discount_value'      => [
                    'required',
                    'numeric',
                    function ($attribute, $value, $fail) use ($discountType) {
                        if ($discountType === 'percent') {
                            if ($value < 1 || $value > 100) {
                                $fail('Phần trăm giảm phải từ 1 đến 100.');
                            }
                        } elseif ($discountType === 'fixed') {
                            if ($value < 1000) {
                                $fail('Số tiền giảm tối thiểu là 1.000đ.');
                            }
                        }
                    }
                ],
                'max_discount_value'  => 'nullable|numeric|min:0',
                'min_total_spent'     => 'nullable|numeric|min:1000',
                'start_date'          => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
                'end_date'            => ['required', 'date', 'after_or_equal:start_date'],
                'description'         => 'nullable|string',
                'status'              => 'required|boolean',
                'usage_limit'         => 'nullable|integer|min:1',
            ],
            [
                'promotion_name.required'     => 'Vui lòng nhập tên chương trình khuyến mãi.',
                'code.required'               => 'Vui lòng nhập mã khuyến mãi.',
                'code.unique'                 => 'Mã khuyến mãi đã tồn tại.',
                'discount_type.required'      => 'Vui lòng chọn loại giảm giá.',
                'discount_value.required'     => 'Vui lòng nhập giá trị giảm.',
                'discount_value.min'          => 'Giá trị giảm phải lớn hơn 0.',
                'max_discount_value.min'      => 'Giá trị giảm tối đa phải lớn hơn hoặc bằng 0.',
                'min_total_spent.required'    => 'Vui lòng nhập mức chi tiêu tối thiểu.',
                'min_total_spent.min'         => 'Chi tiêu tối thiểu phải lớn hơn 1.000đ.',
                'start_date.required'         => 'Vui lòng chọn ngày bắt đầu.',
                'start_date.after_or_equal'   => 'Ngày bắt đầu phải từ hôm nay trở đi.',
                'end_date.required'           => 'Vui lòng chọn ngày kết thúc.',
                'end_date.after_or_equal'     => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
                'status.required'             => 'Vui lòng chọn trạng thái.',
                'usage_limit.integer'         => 'Giới hạn lượt sử dụng phải là số nguyên.',
                'usage_limit.min'             => 'Giới hạn lượt sử dụng phải lớn hơn 0.',
            ]
        );


        $minTotalSpent = $request->min_total_spent;
        $discountType = $request->discount_type;
        $discountValue = $request->discount_value;
        $maxDiscountValue = $request->max_discount_value;

        if ($discountType === 'percent' && $maxDiscountValue && $minTotalSpent) {
            $maxAllowed = $minTotalSpent * ($discountValue / 100);

            if ($maxDiscountValue > $maxAllowed) {
                return back()->withErrors([
                    'max_discount_value' => 'Số tiền giảm tối đa không được vượt quá ' . number_format($maxAllowed, 0, ',', '.') . 'đ (tương ứng ' . $discountValue . '% của tổng đơn tối thiểu).',
                ])->withInput();
            }
        }

        if (empty($validated['usage_limit'])) {
            $validated['usage_limit'] = null;
        }

        $promotion->update([
            'promotion_name'     => $validated['promotion_name'],
            'code'               => $validated['code'],
            'discount_type'      => $validated['discount_type'],
            'discount_value'     => $validated['discount_value'],
            'max_discount_value' => $validated['max_discount_value'],
            'min_total_spent'    => $validated['min_total_spent'],
            'start_date'         => Carbon::parse($validated['start_date']),
            'end_date'           => Carbon::parse($validated['end_date']),
            'description'        => $validated['description'],
            'status'             => $validated['status'],
            'usage_limit'        => $validated['usage_limit'],
        ],);

        event(new \App\Events\PromotionUpdated($promotion));


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
