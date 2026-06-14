<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class QuestionAnswerEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'qanswer_editor_images';
}
