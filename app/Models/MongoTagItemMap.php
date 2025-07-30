<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoTagItemMap extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tag_item_map';
}
