<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'name', 'price', 'quantity_in_stock', 'sku', 'status', 'image'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

?>