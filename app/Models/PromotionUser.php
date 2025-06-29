<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionUser extends Model
{
    use HasFactory;
    
    protected $table = 'promotion_user';

    protected $fillable = [
        'promotion_id',
        'user_id',
        'used_count',
    ];
}
