<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoContactUs extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'contact_us';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
}
