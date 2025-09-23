<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Promotion;
use App\Models\PromotionUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\Input;

class VNPayController extends Controller
{
    public function create(Request $request,$recipient)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $grandTotal = $request->grand_total;
        $recipient_name = $recipient->recipient_name;
        $recipient_phone = $recipient->recipient_phone;
        $recipient_address = $recipient->recipient_address;
        $note = $request->note ?? 0;
        $shipping_fee = $request->shipping_fee;
        $promotion = $request->promotion ?? 0;

        $orderInfo =
            $user->id . '-' . $recipient_name . '-' . $recipient_phone . '-' .
            $recipient_address . '-' . $note . '-' . $shipping_fee . '-' . $grandTotal . '-' . $promotion .'-' . ($request->discount_amount ?? 0);


        // Bắt đầu xử lý redirect qua VNPAY
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_Url = env('VNPAY_URL');
        $vnp_Returnurl = env('VNPAY_RETURN_URL');

        $vnp_TxnRef = 'ORDER' . time();
        $vnp_Amount = $grandTotal * 100;
        $vnp_OrderInfo = $orderInfo;
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

        // Sắp xếp mảng dữ liệu input theo thứ tự bảng chữ cái của key
        ksort($inputData);

        $query = ""; // Biến lưu trữ chuỗi truy vấn (query string)
        $i = 0; // Biến đếm để kiểm tra lần đầu tiên
        $hashdata = ""; // Biến lưu trữ dữ liệu để tạo mã băm (hash data)

        // Duyệt qua từng phần tử trong mảng dữ liệu input
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                // Nếu không phải lần đầu tiên, thêm ký tự '&' trước mỗi cặp key=value
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                // Nếu là lần đầu tiên, không thêm ký tự '&'
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1; // Đánh dấu đã qua lần đầu tiên
            }
            // Xây dựng chuỗi truy vấn
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Gán chuỗi truy vấn vào URL của VNPay
        $vnp_Url = $vnp_Url . "?" . $query;

        // Kiểm tra nếu chuỗi bí mật hash secret đã được thiết lập
        if (isset($vnp_HashSecret)) {
            // Tạo mã băm bảo mật (Secure Hash) bằng cách sử dụng thuật toán SHA-512 với hash secret
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            // Thêm mã băm bảo mật vào URL để đảm bảo tính toàn vẹn của dữ liệu
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }


    public function vnpayReturn(Request $request)
    {
        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TransactionStatus = $request->input('vnp_TransactionStatus');

        // Nếu thanh toán thất bại
        if ($vnp_ResponseCode != '00') {
            $message = $this->getVnpErrorMessage($vnp_ResponseCode);

            return view('clients.vnpay_fail', compact('message'));
        }


        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = $request->all();

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            return view('clients.vnpay_fail');
        }

        if ($inputData['vnp_ResponseCode'] != '00') {
            return view('clients.vnpay_fail');
        }

        try {
            DB::beginTransaction();

            if (!isset($inputData['vnp_OrderInfo'])) {
                return view('clients.vnpay_fail');
            }

            $orderParts = explode('-', $inputData['vnp_OrderInfo']);

            if (count($orderParts) <= 8) {
                return view('clients.vnpay_fail');
            }

            $userId = filter_var(trim($orderParts[0], '"'), FILTER_VALIDATE_INT);

            $user = User::query()->find($userId);

            if (!$user) {
                return view('clients.vnpay_fail');
            }

            $grandTotal = trim($orderParts[6], '"');
            $recipient_name = $orderParts[1];
            $recipient_phone = trim($orderParts[2], '"');
            $recipient_address = $orderParts[3];
            $note = $orderParts[4] == 0 ? '' : $orderParts[4];
            $shipping_fee = $orderParts[5] ?? 0;
            $promotion = $orderParts[7] == 0 ? '' : trim($orderParts[7], '"');
            $cart_user = Cart::with('items.product', 'items.productVariant')->where('user_id', $userId)->first();
            $selectedIds = session()->has('selected_items') ? session('selected_items') : [];
            $discount_amount = trim($orderParts[8], '"');

            $cartItems = !empty($selectedIds)
                ? $cart_user->items->whereIn('id', $selectedIds)
                : $cart_user->items;

            if (!$cart_user) {
                return view('clients.vnpay_fail');
            }

            $order = Order::query()->create([
                'user_id' => $userId,
                'total_price' => $grandTotal,
                'recipient_name' => $recipient_name,
                'recipient_phone' => $recipient_phone,
                'recipient_address' => $recipient_address,
                'note' => $note,
                'shipping_fee' => $shipping_fee,
                'promotion' => $promotion,
                'discount_amount' => $discount_amount,
                'payment_method' => 'vnpay',
                'payment_status' => 'paid',
                'status' => 1
            ]);
            $dataOrderDetail = [];

            foreach ($cartItems as $item) {
                $dataOrderDetail[] = [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->discounted_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $item->product->quantity_in_stock -= $item->quantity;
                $item->product->save();

                if (!is_null($item->productVariant)) {
                    $item->productVariant->quantity_in_stock -= $item->quantity;
                    $item->productVariant->save();
                }
            }

            OrderDetail::query()->insert($dataOrderDetail);

            if ($promotion) {
                $promotion = Promotion::where('code', $promotion)
                    ->where('status', 1)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if ($promotion && ($promotion->usage_limit === null || $promotion->used_count < $promotion->usage_limit)) {
                    // Cập nhật số lần dùng
                    $promotion->increment('used_count');
                    PromotionUser::updateOrCreate(
                        ['promotion_id' => $promotion->id, 'user_id' => $userId],
                        ['used_count' => DB::raw('used_count + 1')]
                    );
                } else {
                    // Nếu mã không hợp lệ hoặc bị xóa thì clear session
                    session()->forget(['promotion', 'promotion_code', 'discount']);
                    $promotionCode = null;
                    $discount = 0;
                }
            }

            // Xóa sản phẩm đã đặt khỏi giỏ hàng
            $cart_user->items()->whereIn('id', $cartItems->pluck('id'))->delete();

            DB::commit();

            return redirect()->route('carts.index')->with('orderSuccess', $order->id);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th->getMessage());
            Log::error(session()->all());
            return view('clients.vnpay_fail');
        }
    }

    private function getVnpErrorMessage($code)
    {
        $messages = [
            '00' => 'Giao dịch thành công.',
            '02' => 'Giao dịch không thành công do tài khoản không tồn tại.',
            '06' => 'Sai số PIN.',
            '24' => 'Khách hàng hủy giao dịch.',
            '91' => 'Ngân hàng không phản hồi.',
            'default' => 'Giao dịch không thành công. Vui lòng thử lại.'
        ];

        return $messages[$code] ?? $messages['default'];
    }


}
