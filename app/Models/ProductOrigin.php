<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrigin extends Model
{
    use HasFactory;

    protected $table = 'product_origins'; // tên bảng

    protected $fillable = ['name']; // các cột có thể ghi
    public function products()
    {
        return $this->hasMany(Product::class, 'origin_id');
    }
}
