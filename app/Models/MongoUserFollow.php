<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoUserFollow extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_follows';

    protected $fillable = [
        'user_1',
        'user_2',
    ];
}
