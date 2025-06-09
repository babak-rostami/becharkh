<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(VideoComment::class, 'parent_id');
    }

    public function replyto()
    {
        return $this->belongsTo(VideoComment::class, 'reply_id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function likes()
    {
        return $this->hasMany(VideoCommentLike::class, 'video_comment_id')->where('like_or_unlike', true);
    }

    public function unLikes()
    {
        return $this->hasMany(VideoCommentLike::class, 'video_comment_id')->where('like_or_unlike', false);
    }

    public function likeAndUnlikes()
    {
        return $this->hasMany(VideoCommentLike::class, 'video_comment_id');
    }
}
