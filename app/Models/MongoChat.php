<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoChat extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'chats';

    protected $fillable = [
        'message',
        'conversation_id',
        'sender_id',
        'created_at',
        'updated_at',
    ];
}
