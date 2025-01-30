<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoUserOrder extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_orders';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
    
}
