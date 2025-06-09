<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'product_name',
        'image',
        'description',
        'ingredients',
        'expiration_date',
        'original_price',
        'discounted_price',
        'status',
        'view',
        'is_show_home',
        'category_id'
    ];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}