<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class SurveyOption extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'survey_options';
}
