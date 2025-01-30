<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoQuestionEmail extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'question_emails';
}
