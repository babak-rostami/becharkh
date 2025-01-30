<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoDistrict extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'districts';
}
