<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoTagItemMap extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'tag_item_map';
}
