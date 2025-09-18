<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderNotificationController extends Controller
{
   
    public function fetch(Request $request)
    {
        $totalCount = Order::where('user_id', auth()->id())
                       ->where('status', 4)
                       ->count();

    $orders = Order::with('orderDetails.product')
                   ->where('user_id', auth()->id())
                   ->where('status', 4)
                   ->latest('created_at')
                   ->get(); 

        $notifications = $orders->map(function ($order) {
            return [
                'id'           => $order->id,
                'order_code'   => $order->order_code,
                'product_image' => $order->orderDetails->first()->product->image 
                          ?? asset('clients/img/no-image.png'),
                'message'      => "Đơn hàng {$order->order_code} đã hoàn thành.",
                'time'         => $order->created_at
                                  ? Carbon::parse($order->created_at)->diffForHumans()
                                  : '',
                'link' => route('clients.orderdetail', $order->id),

            ];
        });

        return response()->json([
        'count' => $totalCount,        
        'notifications' => $notifications
    ]);
    }

    // // Trang chi tiết thông báo đơn hàng
    // public function show($orderId)
    // {
    //     $order = Order::with('orderDetails.product')
    //         ->where('user_id', auth()->id())
    //         ->findOrFail($orderId);

    //     return view('clients.notifications.order', compact('order'));
    // }

    // Trang hiển thị tất cả thông báo đơn hàng
    public function index()
    {
        $orders = Order::with('orderDetails.product')
            ->where('user_id', auth()->id())
            ->where('status', 1) // kiểm tra lại status
            ->latest('created_at')
            ->paginate(10);

        return view('clients.notifications.index', compact('orders'));
    }
}
