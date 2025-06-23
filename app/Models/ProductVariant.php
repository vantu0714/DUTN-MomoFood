<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'price',
        'quantity_in_stock',
        'sku',
        'status',
        'image'
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }




    // Hiển thị giá có định dạng nếu cần dùng
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . ' đ';
    }

    // Lọc các biến thể đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }


    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_values', 'product_variant_id', 'attribute_value_id')
            ->withPivot('price_adjustment');
    }


    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_variant_id');
    }
    public function getFinalPriceAttribute()
    {
        $adjustment = $this->attributeValues->sum(function ($value) {
            return $value->pivot->price_adjustment ?? 0;
        });

        return $this->price + $adjustment;
    }

    public function getFormattedFinalPriceAttribute()
    {
        return number_format($this->final_price, 0, ',', '.') . ' đ';
    }
}
