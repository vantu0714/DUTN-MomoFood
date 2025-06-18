<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_name',
        'discount_type',
        'discount_value',
        'max_discount_value',
        'start_date',
        'end_date',
        'description',
        'status',
        'usage_limit',
        'used_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_value' => 'float',
        'max_discount_value' => 'float',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Kiểm tra mã giảm giá còn hiệu lực hay không
     */
    public function isActive(): bool
    {
        $now = now();

        return $this->status === 'active'
            && $now->between($this->start_date, $this->end_date)
            && (
                is_null($this->usage_limit) || $this->used_count < $this->usage_limit
            );
    }
}
