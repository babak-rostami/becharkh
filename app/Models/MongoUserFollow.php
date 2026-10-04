<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoUserFollow extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'user_follows';

    protected $fillable = [
        'user_1',
        'user_2',
    ];
}
