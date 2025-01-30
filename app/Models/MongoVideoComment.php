<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoVideoComment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'video_comments';

    protected $fillable = [
        'parent_id',
        'reply_to_id',
        'video_id',
        'user_id',
        'body',
        'likes_count',
        'unlikes_count',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(MongoVideoComment::class, 'parent_id');
    }
}
