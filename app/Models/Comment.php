<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
         'product_variant_id', 
        'content',
        'rating',
        'status',
        'image',
        'video',
        'parent_id',
    ];

    // Liên kết đến sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Liên kết đến người dùng
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

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', 1);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}
