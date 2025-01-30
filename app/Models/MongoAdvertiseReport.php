<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoAdvertiseReport extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'advertise_report';
}
