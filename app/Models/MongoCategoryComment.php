<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoCategoryComment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'category_comments';

    protected $fillable = [
        'parent_id',
        'reply_id',
        'user_id',
        'image',
        'body',
        'status',
        'unlike_count',
        'like_count',
        'created_at',
        'updated_at',
        'category_id',
        'items'
    ];

    protected $casts = [
        'parent_id' => 'string',
        'reply_id' => 'string',
        'user_id' => 'string',
        'image' => 'string',
        'body' => 'string',
        'unlikes_count' => 'integer',
        'likes_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'category_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(MongoCategory::class, 'category_id');
    }

    public function replies()
    {
        return $this->hasMany(MongoCategoryComment::class, 'parent_id', '_id')->with('user');
    }

    public function question()
    {
        return $this->belongsTo(MongoQuestion::class, 'question_id');
    }

    public function parent()
    {
        return $this->belongsTo(MongoCategoryComment::class, 'parent_id');
    }

    public function parentReply()
    {
        return $this->belongsTo(MongoCategoryComment::class, 'reply_id');
    }

    public function likes()
    {
        return $this->hasMany(MongoCategoryCommentLike::class, 'comment_id')->where('like_or_unlike', 1);
    }

    public function unlikes()
    {
        return $this->hasMany(MongoCategoryCommentLike::class, 'comment_id')->where('like_or_unlike', 0);
    }

    public function imageFile()
    {
        $images = $this->images ?? [];
        if (count($images) > 0) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $images[0];
        }
    }

    public function getItems()
    {
        if (!isset($this->items)) {
            return collect();
        }
        return MongoItem::whereIn('_id', $this->items)->get();
    }
}
