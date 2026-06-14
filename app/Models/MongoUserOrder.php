<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoUserOrder extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'user_orders';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
    
}
