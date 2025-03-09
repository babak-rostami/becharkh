<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoQuestionAnswer extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'question_answers';

    protected $fillable = [
        'question_id',
        'user_id',
        'parent_id',
        'body',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(MongoQuestionAnswer::class, 'parent_id');
    }

    public function question()
    {
        return $this->belongsTo(MongoQuestion::class, 'question_id');
    }

    public function parent()
    {
        return $this->belongsTo(MongoQuestionAnswer::class, 'parent_id');
    }

    public function parentReply()
    {
        return $this->belongsTo(MongoQuestionAnswer::class, 'reply_id');
    }
}
