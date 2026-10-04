<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class ChangeUsername extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'change_usernames';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
}
