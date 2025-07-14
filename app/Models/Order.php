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
        'total_price',
        'payment_method',
        'payment_status',
        'status',
        'note',
        'reason',
        'recipient_id'
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
        $prefix = 'MOMO'; // Tiền tố cho mã đơn hàng
        $datePart = now()->format('Ymd'); // Ngày tháng năm (ví dụ: 20230626)
        $randomPart = strtoupper(substr(uniqid(), -5)); // 5 ký tự ngẫu nhiên

        do {
            $code = $prefix . $datePart . $randomPart;
            $randomPart = strtoupper(substr(uniqid(), -5));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }
}
