<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOrderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $targetStatus;

    public function __construct(Order $order, $targetStatus)
    {
        $this->order = $order;
        $this->targetStatus = $targetStatus;
    }

    public function handle()
    {
        // Reload order from database to get current status
        $order = Order::find($this->order->id);

        if (!$order) {
            return;
        }

        // Kiểm tra nếu đơn hàng đang ở các trạng thái đặc biệt thì không chuyển tiếp
        if (in_array($order->status, [5, 7, 8])) {
            return;
        }

        $order->status = $this->targetStatus;

        // Chỉ cập nhật thời gian nếu trạng thái thực sự thay đổi
        if ($order->isDirty('status')) {
            switch ($this->targetStatus) {
                case 9: // Đã giao hàng
                    $order->received_at = now();
                    $order->payment_status = 'paid';
                    break;

                case 4: // Hoàn thành
                    $order->completed_at = now();
                    $order->payment_status = 'paid';

                    foreach ($order->orderDetails as $detail) {
                        $product = $detail->product;
                        if ($product) {
                            $product->increment('sold_count', $detail->quantity);
                        }
                    }
                    break;
            }
        }

        $order->save();

        // Lên lịch chuyển tiếp trạng thái chỉ khi không phải các trạng thái đặc biệt
        if ($this->targetStatus == 3 && !in_array($order->status, [5, 7, 8])) {
            UpdateOrderStatus::dispatch($order, 9)
                ->delay(now()->addMinutes(1));
        } elseif ($this->targetStatus == 9 && !in_array($order->status, [5, 7, 8])) {
            UpdateOrderStatus::dispatch($order, 4)
                ->delay(now()->addMinutes(1));
        }
    }
}
