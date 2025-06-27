<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Promotion;
use Illuminate\Http\Request;

class VNPayController extends Controller
{
    public function create($order_id)
{
    $order = Order::with('orderDetails')->findOrFail($order_id);

    $total = 0;
    foreach ($order->orderDetails as $item) {
        $total += $item->price * $item->quantity;
    }

    $shipping = $order->shipping_fee ?? 0;
    $discount = 0;

    if (!empty($order->promotion)) {
        $promotion = Promotion::where('promotion_name', $order->promotion)->first();
        if ($promotion) {
            if ($promotion->discount_type === 'fixed') {
                $discount = $promotion->discount_value;
            } elseif ($promotion->discount_type === 'percent') {
                $discount = $total * ($promotion->discount_value / 100);
                if ($promotion->max_discount_value !== null) {
                    $discount = min($discount, $promotion->max_discount_value);
                }
            }
        }
    }

    $grandTotal = $total + $shipping - $discount;
    if ($grandTotal < 0) $grandTotal = 0;

    // Bắt đầu xử lý redirect qua VNPAY
    $vnp_TmnCode = env('VNPAY_TMN_CODE');
    $vnp_HashSecret = env('VNPAY_HASH_SECRET');
    $vnp_Url = env('VNPAY_URL');
    $vnp_Returnurl = env('VNPAY_RETURN_URL');

    $vnp_TxnRef = $order->id . '-' . time(); // mã đơn hàng
    $vnp_Amount = $grandTotal * 100;
    $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $order->id;
    $vnp_OrderType = 'billpayment';
    $vnp_Locale = 'vn';
    $vnp_BankCode = '';
    $vnp_IpAddr = request()->ip();

    $inputData = [
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef
    ];

    ksort($inputData);
    $hashData = '';
    $query = '';
    $i = 0;
    foreach ($inputData as $key => $value) {
        $hashData .= ($i ? '&' : '') . urlencode($key) . '=' . urlencode($value);
        $query .= urlencode($key) . '=' . urlencode($value) . '&';
        $i++;
    }

    $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    $vnp_Url .= '?' . $query . 'vnp_SecureHash=' . $vnpSecureHash;

    return redirect($vnp_Url);
}


    public function return(Request $request)
    {
        // Kiểm tra kết quả thanh toán
        if ($request->vnp_ResponseCode == '00') {
            // Thành công → lưu đơn hàng từ session vào DB
            $order = session('order_temp');
            $cart = session('cart');

            // TODO: Lưu $order và $cart vào database
            // Order::create([...]);

            // Xoá session nếu cần
            session()->forget(['order_temp', 'cart']);

            return view('clients.vnpay_success'); // hoặc return redirect()->route('order.success');
        } else {
            return view('vnpay_fail'); // hoặc return redirect()->route('order.fail');
        }
    }
}
