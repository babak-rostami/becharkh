<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoChat extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'chats';

    protected $fillable = [
        'message',
        'conversation_id',
        'sender_id',
        'created_at',
        'updated_at',
    ];
}
