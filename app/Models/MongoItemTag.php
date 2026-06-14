<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoItemTag extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'item_tags';

    public function tagItems()
    {
        return $this->hasMany(MongoTagItemMap::class, 'tag_id');
    }
}
