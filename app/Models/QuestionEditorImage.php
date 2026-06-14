<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class QuestionEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'question_editor_images';
}
