<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoItemPrice extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'item_prices';
}
