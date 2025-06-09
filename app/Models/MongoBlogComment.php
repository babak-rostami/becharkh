<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoBlogComment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'blog_comments';

    protected $fillable = [
        'parent_id',
        'reply_id',
        'blog_id',
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
        return $this->hasMany(MongoBlogComment::class, 'parent_id');
    }
}
