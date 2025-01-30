<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoVideoCommentLike extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'video_comment_likes';

    protected $fillable = [
        'comment_id',
        'ip',
        'user_id',
        'like_or_unlike',
        'created_at',
        'updated_at',
    ];
}
