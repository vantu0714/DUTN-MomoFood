<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderNotificationController extends Controller
{
    // Lấy danh sách thông báo (cho popup chuông)
    public function fetch()
    {
        $orders = Order::with('orderDetails.product')
            ->where('user_id', auth()->id())
            ->where('status', 1) // ⚠️ kiểm tra lại: "1" có đúng là đã hoàn tất không?
            ->latest('created_at')
            ->take(5)
            ->get();

        $notifications = $orders->map(function ($order) {
            $firstProduct = $order->orderDetails->first();

            return [
                'id'           => $order->id,
                'order_code'   => $order->order_code,
                'product_image'=> $firstProduct && $firstProduct->product
                                  ? asset('storage/' . $firstProduct->product->image)
                                  : asset('clients/img/no-image.png'),
                'message'      => "Đơn hàng {$order->order_code} đã hoàn tất.",
                'time'         => $order->created_at
                                  ? Carbon::parse($order->created_at)->diffForHumans()
                                  : '',
                'link'         => route('notifications.order.show', $order->id),
            ];
        });

        return response()->json($notifications);
    }

    // Trang chi tiết thông báo đơn hàng
    public function show($orderId)
    {
        $order = Order::with('orderDetails.product')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        return view('clients.notifications.order', compact('order'));
    }

    // Trang hiển thị tất cả thông báo đơn hàng
    public function index()
    {
        $orders = Order::with('orderDetails.product')
            ->where('user_id', auth()->id())
            ->where('status', 1) // ⚠️ kiểm tra lại status
            ->latest('created_at')
            ->paginate(10);

        return view('clients.notifications.index', compact('orders'));
    }
}
