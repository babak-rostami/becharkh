<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class QuestionEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'question_editor_images';
}
