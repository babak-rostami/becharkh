<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoVideoLike extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'video_likes';

    protected $fillable = [
        'video_id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
