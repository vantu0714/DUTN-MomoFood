<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Promotion;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $products = Product::where('status', 1)->get(); // lấy sản phẩm đang hoạt động
    return view('admin.orders.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        DB::beginTransaction();

    try {
        // Bước 1: Tính tổng tiền sản phẩm
        $totalProductPrice = 0;
        foreach ($request->products as $product) {
            $productModel = Product::findOrFail($product['product_id']);
            $price = $productModel->discounted_price;
            $totalProductPrice += $price * $product['quantity'];
        }

        // Bước 2: Áp dụng khuyến mãi (nếu có)
        $discountAmount = 0;

        if ($request->promotion) {
            $promotion = Promotion::where('code', $request->promotion)->first();

            if ($promotion) {
                if ($promotion->type == 'fixed') {
                    $discountAmount = $promotion->value;
                } elseif ($promotion->type == 'percent') {
                    $discountAmount = ($totalProductPrice * $promotion->value) / 100;
                }
            }
        }

        // Bước 3: Tính tổng tiền đơn hàng cuối cùng
        $finalTotal = $totalProductPrice + $request->shipping_fee - $discountAmount;

        // Bước 4: Lưu vào bảng orders
        $order = Order::create([
            'user_id' => $request->user_id,
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'recipient_address' => $request->recipient_address,
            'promotion' => $request->promotion,
            'shipping_fee' => $request->shipping_fee,
            'total_price' => $finalTotal,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'status' => $request->status,
            'note' => $request->note,
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        // Bước 5: Lưu từng sản phẩm vào order_details
        foreach ($request->products as $product) {
            $productModel = Product::findOrFail($product['product_id']);
            $price = $productModel->discounted_price;

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'price' => $price,
                'total' => $price * $product['quantity'],
            ]);
        }

        DB::commit();
        return redirect()->route('orders.index')->with('success', 'Tạo đơn hàng thành công!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
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
