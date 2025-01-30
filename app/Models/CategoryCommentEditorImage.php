<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class CategoryCommentEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ccomment_editor_images';
}
