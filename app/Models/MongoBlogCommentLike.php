<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoBlogCommentLike extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'blog_comment_likes';

    protected $fillable = [
        'comment_id',
        'ip',
        'user_id',
        'like_or_unlike',
        'created_at',
        'updated_at',
    ];
}
