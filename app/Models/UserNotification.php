<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class UserNotification extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'user_notifications';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
}
