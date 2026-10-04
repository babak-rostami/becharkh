<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoVideoLike extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'video_likes';

    protected $fillable = [
        'video_id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
