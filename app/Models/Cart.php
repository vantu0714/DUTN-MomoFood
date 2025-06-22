<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart'; // ⚠️ Đảm bảo không nhầm là 'cart'

    protected $fillable = ['user_id'];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
