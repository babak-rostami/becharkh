<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class QuestionAnswerEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'qanswer_editor_images';
}
