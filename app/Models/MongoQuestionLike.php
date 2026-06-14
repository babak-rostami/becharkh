<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoQuestionLike extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'question_likes';

    protected $fillable = [
        'question_id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
