<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderCancellationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_cancellation_id',
        'order_detail_id'
    ];

    public function cancellation()
    {
        return $this->belongsTo(OrderCancellation::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }
}
