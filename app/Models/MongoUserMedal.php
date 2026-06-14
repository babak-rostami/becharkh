<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoUserMedal extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'user_medals';
}
