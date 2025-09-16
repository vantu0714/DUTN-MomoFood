<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_return_item_id',
        'file_path',
        'file_type'
    ];

    public function returnItem()
    {
        return $this->belongsTo(OrderReturnItem::class);
    }
}
