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
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_variant_id');
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
    public function comboItems()
    {
        return $this->morphMany(ComboItem::class, 'itemable');
    }
    public function getFullNameAttribute()
    {
        $productName = $this->product->product_name ?? 'Không rõ sản phẩm';

        $attrs = $this->attributeValues->map(function ($val) {
            return $val->attribute->name . ': ' . $val->value;
        })->implode(', ');

        return $productName . ($attrs ? " ($attrs)" : '');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/no-image.png');
    }
    public function getFlavorAttribute()
    {
        return optional($this->attributeValues->firstWhere('attribute.name', 'Vị'))->value;
    }

    public function getSizeAttribute()
    {
        return optional($this->attributeValues->firstWhere('attribute.name', 'Size'))->value;
    }
    public function values()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_values', 'product_variant_id', 'attribute_value_id')
            ->withPivot('price_adjustment')
            ->with('attribute') // eager load attribute từ AttributeValue
            ->withTimestamps();
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 1);
    }
    public function scopeActiveInStock($query)
    {
        return $query->where('status', 1)
            ->where('quantity_in_stock', '>', 0);
    }
    public function getSizeIdAttribute()
    {
        return optional($this->values->first(function ($v) {
            return $v->attribute && $v->attribute->name === 'Khối lượng';
        }))->id;
    }
}
