<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_detail_id',
        'quantity',
        'reason',
        'status',
        'admin_note'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function attachments()
    {
        return $this->hasMany(OrderReturnAttachment::class);
    }
}
