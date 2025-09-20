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
            ->whereIn('status', [0, 1, 2, 3, 4])
            ->where('is_read', 0)
            ->count();

        $orders = Order::with('orderDetails.product')
            ->where('user_id', auth()->id())
            ->whereIn('status', [0, 1, 2, 3, 4])
            ->where('is_read', 0)
            ->latest('updated_at')
            ->get();

        $notifications = $orders->map(function ($order) {
            $statusMessages = match ($order->status) {
                0 => "Đơn hàng {$order->order_code} chờ xác nhận.",
                1 => "Đơn hàng {$order->order_code} đang được xác nhận.",
                2 => "Đơn hàng {$order->order_code} đã được xác nhận.",
                3 => "Đơn hàng {$order->order_code} đang được giao.",
                4 => "Đơn hàng {$order->order_code} đã hoàn thành.",
                default => "Đơn hàng {$order->order_code} có cập nhật mới."
            };

            return [
                'id'           => $order->id,
                'order_code'   => $order->order_code,
                'product_image' => $order->orderDetails->first()->product->image
                    ? asset('storage/' . $order->orderDetails->first()->product->image)
                    : asset('clients/img/no-image.png'),
                'message'      => $statusMessages,
                'time'         => $order->updated_at
                    ? Carbon::parse($order->updated_at)->diffForHumans()
                    : '',
                'link' => route('clients.orderdetail', $order->id),
                'is_read'      => $order->is_read,


            ];
        });

        return response()->json([
            'count' => $totalCount,
            'notifications' => $notifications
        ]);
    }
    public function index()
    {
        $orders = Order::with('orderDetails.product')
            ->where('user_id', auth()->id())
            ->whereIn('status', [0, 1, 2, 3, 4])
            ->latest('created_at')
            ->paginate(10);

        return view('clients.notifications.index', compact('orders'));
    }

    public function markAsRead($id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);
        $order->is_read = 1;
        $order->save();

        return response()->json(['success' => true]);
    }
}
