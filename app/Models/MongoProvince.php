<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoProvince extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'provinces';
}
