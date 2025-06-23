<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    public function index()
    {
        //
        $orders = Order::latest()->paginate(10);
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
                'status' => $request->status ?? 'pending',
                'note' => $request->note,
                'cancellation_reason' => $request->cancellation_reason,
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
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Tạo đơn hàng thành công!');
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
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
