<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoBlogLike extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'blog_likes';

    protected $fillable = [
        'blog_id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
