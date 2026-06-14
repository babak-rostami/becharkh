<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class CategoryCommentEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'ccomment_editor_images';
}
