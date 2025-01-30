<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoContactUs extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contact_us';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
}
