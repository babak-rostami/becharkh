<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class UserNotification extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_notifications';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
}
