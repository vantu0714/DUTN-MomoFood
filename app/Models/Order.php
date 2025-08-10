<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    // app/Models/Order.php
    protected $fillable = [
        'order_code',
        'user_id',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'promotion',
        'shipping_fee',
        'discount_amount',
        'total_price',
        'payment_method',
        'payment_status',
        'status',
        'note',
        'reason',
        'recipient_id',
        'return_reason',
        'return_approved',
        'return_rejection_reason',
        'return_requested_at',
        'return_processed_at'
    ];

    protected $dates = [
        'completed_at',
        'return_requested_at'
    ];

    protected $casts = [
        'shipping_started_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_code = self::generateOrderCode();
        });
    }

    public static function generateOrderCode()
    {
        $prefix = 'MMF'; // Tiền tố cho mã đơn hàng
        $datePart = now()->format('Ymd'); // Ngày tháng năm (ví dụ: 20230626)
        $randomPart = strtoupper(substr(uniqid(), -5)); // 5 ký tự ngẫu nhiên

        do {
            $code = $prefix . $datePart . $randomPart;
            $randomPart = strtoupper(substr(uniqid(), -5));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
