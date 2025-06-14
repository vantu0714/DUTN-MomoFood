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
            // Bước 1: Tính tổng tiền sản phẩm
            $totalProductPrice = 0;
            $orderDetails = [];
        
            foreach ($request->products as $productInput) {
                if (empty($productInput['product_id']) || empty($productInput['quantity'])) {
                    continue;
                }
            
                $product = Product::findOrFail($productInput['product_id']);
                $quantity = (int) $productInput['quantity'];
            
                // Kiểm tra có chọn variant không
                $variantId = $productInput['product_variant_id'] ?? null;
            
                if ($variantId) {
                    $variant = ProductVariant::where('product_id', $product->id)
                                             ->where('id', $variantId)
                                             ->firstOrFail();
                    $price = $variant->price;
                } else {
                    $price = $product->discounted_price ?? $product->price;
                }
            
                $total = $price * $quantity;
                $totalProductPrice += $total;
            
                $orderDetails[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId, // có hoặc null
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }
            
        
            // Bước 2: Áp dụng khuyến mãi (nếu có)
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
        
            // Bước 3: Tính tổng tiền đơn hàng
            $shippingFee = (float) $request->shipping_fee;
            $finalTotal = $totalProductPrice + $shippingFee - $discountAmount;
        
            // Bước 4: Lưu đơn hàng
            $order = Order::create([
                'user_id' => $request->user_id,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_address' => $request->recipient_address,
                'promotion' => $promotionCode,
                'shipping_fee' => $shippingFee,
                'total_price' => $finalTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'status' => $request->status,
                'note' => $request->note,
                'cancellation_reason' => $request->cancellation_reason,
            ]);
        
            // Bước 5: Lưu chi tiết đơn hàng
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
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
        
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $order = Order::with(['user', 'orderDetails.productVariant.product'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
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
