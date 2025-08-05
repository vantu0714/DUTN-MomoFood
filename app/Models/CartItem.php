<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items'; // ⚠️ thêm dòng này nếu chưa có

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'original_price',
        'discounted_price',
        'total_price',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

   public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function getIsOutOfStockAttribute()
{
    if ($this->product_variant_id) {
        return ($this->productVariant?->quantity_in_stock ?? 0) <= 0;
    } else {
        return ($this->product?->quantity_in_stock ?? 0) <= 0;
    }
}

    
}
