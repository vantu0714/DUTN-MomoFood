<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'note',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
