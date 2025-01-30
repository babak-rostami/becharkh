<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoQuestionLike extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'question_likes';

    protected $fillable = [
        'question_id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
