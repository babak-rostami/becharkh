<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertiseComment extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function advertise()
    {
        return $this->belongsTo(Advertise::class, 'advertise_id');
    }

    public function likes()
    {
        return $this->hasMany(AdvertiseCommentLike::class, 'advertise_comment_id')->where('like_or_unlike', true)->get();
    }

    public function unLikes()
    {
        return $this->hasMany(AdvertiseCommentLike::class, 'advertise_comment_id')->where('like_or_unlike', false)->get();
    }

    public function replies()
    {
        return $this->hasMany(AdvertiseComment::class, 'parent_id');
    }

    public function replyto()
    {
        return $this->belongsTo(AdvertiseComment::class, 'reply_to_id');
    }
}
