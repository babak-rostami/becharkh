<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoProvince extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'provinces';
}
