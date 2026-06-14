<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoBlogLike extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'blog_likes';

    protected $fillable = [
        'blog_id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
