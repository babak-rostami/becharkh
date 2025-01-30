<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ProductCommentEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'product_comment_editor_images';
}
