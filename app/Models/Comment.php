<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'content',
        'rating'
    ];
    // Liên kết đến người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Liên kết đến sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // Scope để lọc những bình luận có rating (testimonials)
    public function scopeHasRating($query)
    {
        return $query->where('rating', '>', 0);
    }
}
