<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class SurveyOption extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'survey_options';
}
