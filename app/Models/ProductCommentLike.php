<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class ProductCommentLike extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'product_comment_likes';
}
