<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoCategoryCommentLike extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'category_comment_likes';

    protected $fillable = [
        'comment_id',
        'ip',
        'user_id',
        'like_or_unlike',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'ip' => 'string',
        'user_id' => 'integer',
        'like_or_unlike' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
