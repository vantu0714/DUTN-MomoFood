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
    
}

?>