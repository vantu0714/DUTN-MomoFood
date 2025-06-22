<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboItem extends Model
{
    protected $fillable = [
        'combo_id',
        'itemable_id',
        'itemable_type',
        'quantity',
    ];

    public function combo()
    {
        return $this->belongsTo(Product::class, 'combo_id');
    }

    public function itemable()
    {
        return $this->morphTo();
    }
    
}
