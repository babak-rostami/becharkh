<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoCity extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cities';
}
