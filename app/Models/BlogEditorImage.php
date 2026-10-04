<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class BlogEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'blog_editor_images';
}
