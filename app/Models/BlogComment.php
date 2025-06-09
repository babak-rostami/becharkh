<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function email()
    {
        if (isset($this->user)) {
            return $this->user->email;
        } else {
            return $this->email;
        }
    }

    public function replies()
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }

    public function replyto()
    {
        return $this->belongsTo(BlogComment::class, 'reply_id');
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function likes()
    {
        return $this->hasMany(BlogCommentLike::class, 'blog_comment_id')->where('like_or_unlike', true);
    }

    public function unLikes()
    {
        return $this->hasMany(BlogCommentLike::class, 'blog_comment_id')->where('like_or_unlike', false);
    }

    public function likeAndUnlikes()
    {
        return $this->hasMany(BlogCommentLike::class, 'blog_comment_id');
    }
}
