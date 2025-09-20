<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'refund_amount',
        'cancelled_at'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderCancellationItem::class);
    }
}
