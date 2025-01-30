<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ProductCommentLike extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'product_comment_likes';
}
