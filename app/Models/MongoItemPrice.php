<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoItemPrice extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'item_prices';
}
