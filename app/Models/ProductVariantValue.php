<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantValue extends Model
{
    protected $fillable = ['product_variant_id', 'attribute_value_id'];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
    
}
