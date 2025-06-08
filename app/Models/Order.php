<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    // app/Models/Order.php
    protected $fillable = [
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
        'cancellation_reason',
    ];
}
