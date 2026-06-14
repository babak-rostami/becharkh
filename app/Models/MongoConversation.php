<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoConversation extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'conversation';

    public function messages()
    {
        return $this->hasMany(MongoChat::class, 'conversation_id');
    }

    public function user2()
    {
        return $this->belongsTo(MongoUser::class, $this->user_1 == auth('user')->id() ? 'user_2' : 'user_1');
    }
}
