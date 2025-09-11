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
        'rating',
        'status',
        'image',
        'video', // Trạng thái bình luận: 1 - hiển thị, 0 - ẩn
    ];
    // Liên kết đến người dùng
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope để lọc những bình luận có rating (testimonials)
    public function scopeHasRating($query)
    {
        return $query->where('rating', '>', 0);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
