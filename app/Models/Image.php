<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['comment_id', 'path'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
