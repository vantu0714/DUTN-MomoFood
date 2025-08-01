<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AttributeValue extends Model

{
    
    protected $fillable = ['attribute_id', 'value','name'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values');
    }
   
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }
}


