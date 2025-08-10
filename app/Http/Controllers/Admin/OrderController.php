<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateOrderStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Lọc theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'like', '%' . $keyword . '%')
                    ->orWhere('recipient_name', 'like', '%' . $keyword . '%')
                    ->orWhere('recipient_phone', 'like', '%' . $keyword . '%');
            });
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_status')) {
            if ($request->payment_status == 'paid') {
                $query->where('payment_method', '!=', 'cod');
            } elseif ($request->payment_status == 'unpaid') {
                $query->where('payment_method', 'cod');
            }
        }

        // Lọc theo trạng thái đơn hàng
        if ($request->filled('order_status') && $request->order_status != 'all') {
            $query->where('status', $request->order_status);
        }

        $orders = $query->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        //
        $products = Product::with('variants')->get();
        $customers = User::all();

        return view('admin.orders.create', compact('products', 'customers'));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $totalProductPrice = 0;
            $orderDetails = [];

            foreach ($request->products as $productInput) {
                // Bỏ qua nếu không có product_id hoặc số lượng <= 0
                if (empty($productInput['product_id']) || (int) $productInput['quantity'] <= 0) {
                    continue;
                }

                // Tìm sản phẩm
                $product = Product::find($productInput['product_id']);
                if (!$product) {
                    continue;
                }

                $quantity = (int) $productInput['quantity'];
                $variantId = $productInput['product_variant_id'] ?? null;
                $price = $product->discounted_price ?? $product->price;

                // Nếu có biến thể → lấy giá từ biến thể
                if ($variantId) {
                    $variant = ProductVariant::where('product_id', $product->id)
                        ->where('id', $variantId)
                        ->first();

                    if ($variant) {
                        $price = $variant->price;
                    }
                }

                $totalProductPrice += $price * $quantity;

                $orderDetails[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }

            if (empty($orderDetails)) {
                return back()->withInput()->with('error', 'Vui lòng chọn ít nhất một sản phẩm hợp lệ.');
            }

            // Khuyến mãi
            $discountAmount = 0;
            $promotionCode = $request->promotion;
            if (!empty($promotionCode)) {
                $promotion = Promotion::where('code', $promotionCode)->first();

                if ($promotion) {
                    if ($promotion->type === 'fixed') {
                        $discountAmount = $promotion->value;
                    } elseif ($promotion->type === 'percent') {
                        $discountAmount = ($totalProductPrice * $promotion->value) / 100;
                    }
                }
            }

            $shippingFee = (float) $request->shipping_fee;
            $finalTotal = $totalProductPrice + $shippingFee - $discountAmount;

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $request->user_id,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_address' => $request->recipient_address,
                'promotion' => $promotionCode,
                'shipping_fee' => $shippingFee,
                'total_price' => $finalTotal,
                'payment_method' => $request->payment_method ?? 'cod',
                'payment_status' => $request->payment_status ?? 'unpaid',
                'status' => $request->status ?? 1,
                'note' => $request->note,
                'reason' => $request->reason,
            ]);

            // Lưu chi tiết đơn hàng
            foreach ($orderDetails as $detail) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $detail['product_id'],
                    'product_variant_id' => $detail['product_variant_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);

                Product::where('id', $detail['product_id'])->increment('sold_count', $detail['quantity']);
            }

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Tạo đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $order = Order::with('orderDetails.product', 'orderDetails.productVariant.product')->find($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $order = Order::with(['user', 'orderDetails.product', 'orderDetails.productVariant'])->findOrFail($id);
        $customers = User::whereHas('role', fn($q) => $q->where('name', 'user'))->get();
        $products = Product::with('variants')->get();

        return view('admin.orders.edit', compact('order', 'customers', 'products'));
        // return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $order = Order::findOrFail($id);
        $order->load('orderDetails');
        $status = $request->status;
        $paymentStatus = $request->payment_status;

        // Nếu đơn hàng đã giao thành công, thì auto đánh dấu đã thanh toán
        if ($status == 4) {
            $paymentStatus = 'paid';
            foreach ($order->orderDetails as $detail) {
                Product::where('id', $detail->product_id)->increment('sold_count', $detail->quantity);
            }
        }

        // Nếu bị hủy thì mới cần lý do hủy, ngược lại set null
        $reason = $status == 6 ? $request->reason : null;

        $order->update([
            'status' => $status,
            'payment_status' => $paymentStatus,
            'note' => $request->note,
            'reason' => $reason,
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật thành công');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|integer|min:1|max:9',
        ]);

        $newStatus = (int) $request->status;

        // Không cho phép chuyển từ trạng thái Hoàn hàng (5) sang Hủy đơn (6)
        if ($order->status == 5 && $newStatus == 6) {
            return back()->with('error', 'Không thể hủy đơn hàng sau khi đã hoàn hàng.');
        }

        // Nếu chuyển sang trạng thái Đang giao (3) → lên lịch chuyển sang Đã giao (9) sau 1 phút
        if ($newStatus == 3) {
            $order->status = 3;
            $order->delivered_at = now();
            $order->save();

            // Lên lịch chuyển sang trạng thái Đã giao hàng sau 1 phút
            UpdateOrderStatus::dispatch($order, 9)
                ->delay(now()->addMinutes(1));

            return back()->with('success', 'Đơn hàng đang được giao và sẽ tự động cập nhật trạng thái sau 1 phút.');
        }

        // Nếu chuyển sang trạng thái Hoàn thành (4) → auto paid
        elseif ($newStatus == 4) {
            $order->status = 4;
            $order->completed_at = now();
            $order->payment_status = 'paid';

            foreach ($order->orderDetails as $detail) {
                $product = Product::find($detail->product_id);

                if ($product) {
                    // Nếu là sản phẩm đơn (simple) → trừ tồn kho trực tiếp
                    if ($product->product_type === 'simple') {
                        $product->decrement('quantity_in_stock', $detail->quantity);
                    }

                    // Nếu là sản phẩm có biến thể → trừ tồn kho ở bảng product_variants
                    elseif ($product->product_type === 'variant' && $detail->product_variant_id) {
                        DB::table('product_variants')
                            ->where('id', $detail->product_variant_id)
                            ->decrement('quantity_in_stock', $detail->quantity);
                    }

                    // Tăng số lượng đã bán cho sản phẩm chính
                    $product->increment('sold_count', $detail->quantity);
                }
            }

            $order->save();

            // Chỉ tính đơn hàng Hoàn thành (4) để xét VIP
            $userId = $order->user_id;
            $totalSpent = Order::where('user_id', $userId)
                ->where('status', 4) // chỉ tính đơn hoàn thành
                ->sum('total_price');

            if ($totalSpent >= 5000000) {
                User::where('id', $userId)->update(['is_vip' => true]);
            }

            return back()->with('success', 'Trạng thái đơn hàng đã được cập nhật và kiểm tra VIP.');
        }
        // Nếu chuyển sang trạng thái Hoàn hàng (5) → bắt buộc có lý do
        elseif ($newStatus == 5) {
            $request->validate([
                'reason' => 'required|string|max:1000',
            ]);

            $order->status = 5;
            $order->reason = $request->reason;
        } elseif ($order->status == 3 && $newStatus == 9) {
            $order->status = 9;
            $order->received_at = now();
        }
        // Các trạng thái khác
        else {
            $order->status = $newStatus;
        }

        $order->save();

        return back()->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($order->status >= 4) {
            return back()->with('error', 'Đơn hàng đã xử lý xong, không thể hủy.');
        }

        $order->status = 6; // hủy đơn
        $order->reason = $request->reason;
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Đã hủy đơn hàng.');
    }

    public function approveReturn($id)
    {
        $order = Order::findOrFail($id);

        // Kiểm tra trạng thái hiện tại phải là "Chờ xử lý hoàn hàng" (7)
        if ($order->status != 7) {
            return back()->with('error', 'Đơn hàng không ở trạng thái chờ xử lý hoàn hàng.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 5,
                'payment_status' => 'refunded',
                'return_approved' => true,
                'return_processed_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Đã chấp nhận yêu cầu hoàn hàng và cập nhật trạng thái hoàn tiền.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function rejectReturn(Request $request, $id)
    {
        $request->validate([
            'return_rejection_reason' => 'required|string|min:10|max:1000',
        ]);

        if ($request->has('return_rejection_reason')) {
            $request->validate([
                'return_rejection_reason' => 'required|string|min:10|max:1000',
            ]);
        }

        $order = Order::findOrFail($id);

        if ($order->status != 7) {
            return back()->with('error', 'Đơn hàng không ở trạng thái chờ xử lý hoàn hàng.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 8,
                'return_rejection_reason' => $request->return_rejection_reason,
                'return_processed_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Đã từ chối yêu cầu hoàn hàng.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
