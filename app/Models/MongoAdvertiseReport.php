<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoAdvertiseReport extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'advertise_report';
}
