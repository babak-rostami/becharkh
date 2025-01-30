<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class BlogEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'blog_editor_images';
}
