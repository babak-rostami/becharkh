<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class ProductCommentEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'product_comment_editor_images';
}
