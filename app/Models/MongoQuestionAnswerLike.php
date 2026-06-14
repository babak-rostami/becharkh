<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoQuestionAnswerLike extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'question_answer_likes';

    protected $fillable = [
        'answer_id',
        'ip',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
