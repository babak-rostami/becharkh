<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoQuestionEmail extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'question_emails';
}
