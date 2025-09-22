<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateOrderStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderReturnItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
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
                $query->where('payment_status', '!=', 'cod');
            } elseif ($request->payment_status == 'unpaid') {
                $query->where('payment_status', 'cod');
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

    public function show(string $id)
    {
        $order = Order::with([
            'orderDetails.product',
            'orderDetails.productVariant.attributeValues.attribute',
            'returnItems.orderDetail.product',
            'returnItems.orderDetail.productVariant.attributeValues.attribute',
            'returnItems.attachments'
        ])->find($id);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(string $id)
    {
        //
        $order = Order::with(['user', 'orderDetails.product', 'orderDetails.productVariant'])->findOrFail($id);
        $customers = User::whereHas('role', fn($q) => $q->where('name', 'user'))->get();
        $products = Product::with('variants')->get();

        return view('admin.orders.edit', compact('order', 'customers', 'products'));
        // return view('admin.orders.edit', compact('order'));
    }

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
                // Nếu có biến thể thì trừ ở bảng product_variants
                if ($detail->product_variant_id) {
                    DB::table('product_variants')
                        ->where('id', $detail->product_variant_id)
                        ->decrement('quantity_in_stock', $detail->quantity);
                } else {
                    // Nếu sản phẩm thường thì trừ trực tiếp trong bảng products
                    Product::where('id', $detail->product_id)
                        ->decrement('quantity_in_stock', $detail->quantity);
                }

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
            'status' => 'required|integer|min:1|max:11',
        ]);

        $newStatus = (int) $request->status;
        $currentStatus = $order->status;

        if ($request->update_source === 'admin_ui') {
            // Danh sách các trạng thái được phép chuyển đổi từ trạng thái hiện tại
            $allowedTransitions = [
                1 => [2, 6], // Chưa xác nhận → Đã xác nhận hoặc Hủy
                2 => [3], // Đã xác nhận → Đang giao hoặc Hủy
                3 => [9, 11], // Đang giao → Đã giao hoặc Giao hàng thất bại
                5 => [],    // Hoàn hàng → Không thể chuyển tiếp
                6 => [],    // Hủy → Không thể chuyển tiếp
                9 => [4],   // Đã giao → Hoàn thành
                10 => [],   // Không xác nhận → Không thể chuyển tiếp
                11 => [],   // Giao hàng thất bại → Không thể chuyển tiếp
            ];

            // Kiểm tra tính hợp lệ của chuyển trạng thái
            if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
                return back()->with('error', 'Không thể chuyển từ trạng thái hiện tại sang trạng thái này.');
            }

            // Không cho phép chuyển từ Hoàn hàng (5) sang Hủy (6)
            if ($currentStatus == 5 && $newStatus == 6) {
                return back()->with('error', 'Không thể hủy đơn hàng sau khi đã hoàn hàng.');
            }
        }

        // Đang giao (3) →  Đã giao (9)
        if ($newStatus == 3) {
            $order->status = 3;
            $order->delivered_at = now();
            $order->save();

            return back()->with('success', 'Đơn hàng đang được giao...');
        } elseif ($newStatus == 9) {
            $order->status = 9;
            $order->received_at = now();
            $order->payment_status = 'paid';
            $order->save();

            return back()->with('success', 'Đã cập nhật trạng thái đã giao hàng.');
        }
        // Giao hàng thất bại (11) → Xử lý với lý do
        elseif ($newStatus == 11) {
            $request->validate(['reason' => 'required|string|max:1000']);

            // Nếu đã thanh toán thì chuyển sang trạng thái hoàn tiền
            $refundMessage = '';
            if ($order->payment_status === 'paid') {
                $order->payment_status = 'refunded';
                $refundMessage = ' Đã thực hiện hoàn tiền.';
            }

            $order->status = 11;
            $order->reason = $request->reason;
            $order->delivery_failed_at = now();
            $order->save();

            $order->load('orderDetails.product', 'orderDetails.productVariant');

            // Lấy danh sách các sản phẩm đã bị hủy
            $cancelledItemIds = [];
            if ($order->cancellation) {
                $cancelledItemIds = $order->cancellation->items->pluck('order_detail_id')->toArray();
            }

            foreach ($order->orderDetails as $orderDetail) {
                // Chỉ hoàn kho nếu sản phẩm này CHƯA bị hủy
                if (!in_array($orderDetail->id, $cancelledItemIds)) {
                    $orderDetail->product->quantity_in_stock += $orderDetail->quantity;
                    $orderDetail->product->save();

                    if (!is_null($orderDetail->productVariant)) {
                        $orderDetail->productVariant->quantity_in_stock += $orderDetail->quantity;
                        $orderDetail->productVariant->save();
                    }
                }
            }

            return back()->with('success', 'Đã đánh dấu giao hàng thất bại.' . $refundMessage);
        }
        // Hoàn thành (4) → Xử lý tồn kho và VIP
        elseif ($newStatus == 4) {
            $order->status = 4;
            $order->completed_at = now();
            $order->save();

            $productIds = $order->orderDetails->pluck('product_id')->unique();

            foreach ($productIds as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $product->increment('sold_count', 1);
                }
            }
        }


        // Hoàn hàng (5) → Yêu cầu lý do
        elseif ($newStatus == 5) {
            $request->validate(['reason' => 'required|string|max:1000']);

            // Nếu đã thanh toán thì chuyển sang trạng thái hoàn tiền
            if ($order->payment_status === 'paid') {
                $order->payment_status = 'refunded';
                $refundMessage = " Đã thực hiện hoàn tiền.";
            } else {
                $refundMessage = "";
            }

            $order->status = 5;
            $order->reason = $request->reason;

            $order->return_processed_at = now();

            $order->save();

            return back()->with('success', 'Đã xác nhận hoàn hàng.' . $refundMessage);
        }
        // Các trường hợp khác
        else {
            $order->status = $newStatus;
        }

        $order->save();
        return back()->with('success', 'Cập nhật thành công!');
    }

    public function reject(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($order->status != 1) {
            return back()->with('error', 'Chỉ có thể không xác nhận đơn hàng ở trạng thái chưa xác nhận.');
        }

        DB::beginTransaction();
        try {
            $refundMessage = '';
            if ($order->payment_status === 'paid') {
                $order->payment_status = 'refunded';
                $refundMessage = ' Đã thực hiện hoàn tiền.';
            }

            $order->load('orderDetails.product', 'orderDetails.productVariant');

            // Lấy danh sách các order_detail_id đã bị hủy
            $cancelledItemIds = [];
            if ($order->cancellation) {
                $cancelledItemIds = $order->cancellation->items->pluck('order_detail_id')->toArray();
            }

            foreach ($order->orderDetails as $orderDetail) {
                // Chỉ hoàn kho nếu sản phẩm này CHƯA bị hủy
                if (!in_array($orderDetail->id, $cancelledItemIds)) {
                    $orderDetail->product->quantity_in_stock += $orderDetail->quantity;
                    $orderDetail->product->save();

                    if (!is_null($orderDetail->productVariant)) {
                        $orderDetail->productVariant->quantity_in_stock += $orderDetail->quantity;
                        $orderDetail->productVariant->save();
                    }
                }
            }

            $order->update([
                'status' => 10, // Không xác nhận
                'reason' => $request->reason,
                'payment_status' => $order->payment_status,
            ]);

            DB::commit();

            if ($order->payment_status === 'refunded') {
                return redirect()->route('admin.orders.show', $order->id)
                    ->with('success', 'Đã không xác nhận đơn hàng.' . $refundMessage);
            } else {
                return redirect()->route('admin.orders.show', $order->id)
                    ->with('success', 'Đã không xác nhận đơn hàng.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function updateOrderReturnStatus($orderId)
    {
        $order = Order::with('returnItems')->findOrFail($orderId);

        $pendingCount = $order->returnItems->where('status', 'pending')->count();
        $approvedCount = $order->returnItems->where('status', 'approved')->count();
        $rejectedCount = $order->returnItems->where('status', 'rejected')->count();
        $totalCount = $order->returnItems->count();

        // Nếu vẫn còn sản phẩm chờ xử lý, giữ nguyên trạng thái
        if ($pendingCount > 0) {
            return;
        }

        if ($approvedCount > 0 && $rejectedCount > 0) {
            // Có cả sản phẩm được chấp nhận và từ chối - Hoàn hàng một phần
            $updateData = [
                'status' => 12, // Hoàn hàng một phần
                'return_processed_at' => now()
            ];

            // Nếu đã thanh toán thì chuyển thành hoàn tiền
            if ($order->payment_status === 'paid') {
                $updateData['payment_status'] = 'refunded';
            }

            $order->update($updateData);
        } elseif ($approvedCount === $totalCount) {
            // Tất cả sản phẩm được chấp nhận - Hoàn hàng toàn bộ
            $updateData = [
                'status' => 5, // Hoàn hàng toàn bộ
                'return_processed_at' => now()
            ];

            // Nếu đã thanh toán thì chuyển thành hoàn tiền
            if ($order->payment_status === 'paid') {
                $updateData['payment_status'] = 'refunded';
            }

            $order->update($updateData);
        } elseif ($rejectedCount === $totalCount) {
            // Tất cả sản phẩm bị từ chối - Hoàn hàng thất bại
            $order->update([
                'status' => 8, // Hoàn hàng thất bại
                'return_processed_at' => now()
            ]);
            // Không thay đổi trạng thái thanh toán khi từ chối hoàn hàng
        }
    }

    public function approveReturnItem(Request $request, $id)
    {
        $returnItem = OrderReturnItem::with(['orderDetail', 'order'])->findOrFail($id);

        DB::beginTransaction();
        try {
            $returnItem->update([
                'status' => 'approved',
                'admin_note' => $request->admin_note
            ]);

            // Cập nhật trạng thái tổng thể của đơn hàng
            $this->updateOrderReturnStatus($returnItem->order_id);

            DB::commit();

            return back()->with('success', 'Đã chấp nhận yêu cầu hoàn hàng cho sản phẩm: ' . $returnItem->orderDetail->product->product_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function rejectReturnItem(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000'
        ]);

        $returnItem = OrderReturnItem::with(['orderDetail', 'order'])->findOrFail($id);

        DB::beginTransaction();
        try {
            $returnItem->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note
            ]);

            // Cập nhật trạng thái tổng thể của đơn hàng
            $this->updateOrderReturnStatus($returnItem->order_id);

            DB::commit();

            return back()->with('success', 'Đã từ chối yêu cầu hoàn hàng cho sản phẩm: ' . $returnItem->orderDetail->product->product_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function approveReturn($id)
    {
        $order = Order::with('returnItems')->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($order->returnItems as $returnItem) {
                if ($returnItem->status == 'pending') {
                    $returnItem->update([
                        'status' => 'approved'
                    ]);
                }
            }

            $updateData = [
                'status' => 5, // Hoàn hàng
                'return_processed_at' => now()
            ];

            if ($order->payment_status === 'paid') {
                $updateData['payment_status'] = 'refunded';
            }

            $order->update($updateData);

            DB::commit();

            $message = 'Đã chấp nhận tất cả yêu cầu hoàn hàng.';
            if ($order->payment_status === 'paid') {
                $message .= ' Đã thực hiện hoàn tiền.';
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function rejectReturn(Request $request, $id)
    {
        $request->validate([
            'return_rejection_reason' => 'required|string|max:1000'
        ]);

        $order = Order::with('returnItems')->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($order->returnItems as $returnItem) {
                if ($returnItem->status == 'pending') {
                    $returnItem->update([
                        'status' => 'rejected',
                        'admin_note' => $request->return_rejection_reason
                    ]);
                }
            }

            // Cập nhật trạng thái đơn hàng
            $order->update([
                'status' => 8, // Hoàn hàng thất bại
                'return_rejection_reason' => $request->return_rejection_reason,
                'return_processed_at' => now()
            ]);

            DB::commit();

            return back()->with('success', 'Đã từ chối tất cả yêu cầu hoàn hàng.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
