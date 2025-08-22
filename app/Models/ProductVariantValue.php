<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantValue extends Model
{
    protected $fillable = ['product_variant_id', 'attribute_value_id', 'price_adjustment'];


    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
    public function attribute()
    {
        return $this->hasOneThrough(
            Attribute::class,
            AttributeValue::class,
            'id',             // attribute_values.id
            'id',             // attributes.id
            'attribute_value_id', // product_variant_values.attribute_value_id
            'attribute_id'    // attribute_values.attribute_id
        );
    }
}
