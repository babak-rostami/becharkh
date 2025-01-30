<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailComment extends Model
{
    use HasFactory;

    public function replies()
    {
        return $this->hasMany(DetailComment::class, 'parent_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(DetailComment::class, 'reply_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
