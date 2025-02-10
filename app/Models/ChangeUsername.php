<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ChangeUsername extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'change_usernames';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
}
