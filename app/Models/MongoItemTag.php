<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoItemTag extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'item_tags';

    public function tagItems()
    {
        return $this->hasMany(MongoTagItemMap::class, 'tag_id');
    }
}
