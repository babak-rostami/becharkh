<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ProductComment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'product_comments';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(ProductComment::class, 'parent_id');
    }
}
