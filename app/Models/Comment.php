<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function advertise()
    {
        return $this->belongsTo(Advertise::class, 'advertise_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function replyToWhich()
    {
        return $this->belongsTo(Comment::class, 'reply_id');
    }

}
