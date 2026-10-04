<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoCity extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'cities';
}
