<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoDistrict extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'districts';
}
