<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class ProductComment extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'product_comments';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(ProductComment::class, 'parent_id');
    }
}
