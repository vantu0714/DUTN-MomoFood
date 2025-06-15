<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id', 
        'product_variant_id',
        'quantity',
        'price',
        'total',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getProductNameDisplayAttribute()
    {
        if ($this->product_variant_id && $this->productVariant) {
            // Có biến thể → lấy tên từ product trong variant
            return $this->productVariant->product->product_name ?? 'Không rõ sản phẩm';
        }

        // Không có biến thể → lấy từ product trực tiếp
        return $this->product->product_name ?? 'Không rõ sản phẩm';
    }

    public function getVariantNameDisplayAttribute()
    {
        return $this->product_variant_id && $this->productVariant
            ? $this->productVariant->name
            : 'Không có biến thể';
    }
}
