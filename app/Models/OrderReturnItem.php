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

    public function scopeProcessed($query)
    {
        return $query->where('status', '!=', 'pending');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isProcessed()
    {
        return $this->status != 'pending';
    }

    public function isApproved()
    {
        return $this->status == 'Đồng Ý';
    }

    public function isRejected()
    {
        return $this->status == 'Từ Chối';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'approved' => 'Đồng Ý',
            'rejected' => 'Từ Chối',
            'pending' => 'Chờ Xử Lý',
            default => ucfirst($this->status),
        };
    }
}
