<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;


    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function lastMessage()
    {
        return $this->messages->last();
    }

    public function isLastMessageYou()
    {
        if ($this->lastMessage() != null && $this->lastMessage()->sender_id == auth('user')->id()) {
            return true;
        } else {
            return false;
        }
    }

    public function youHaveUnreadMessage()
    {
        if ($this->lastMessage() != null && !$this->isLastMessageYou() && $this->lastMessage()->seen_at == null) {
            return true;
        } else {
            return false;
        }
    }

    public function user1()
    {
        return $this->hasOne(User::class, 'id', 'user_1');
    }
    public function user2()
    {
        return $this->hasOne(User::class, 'id', 'user_2');
    }

    public function toUser()
    {
        if ($this->user_1 == auth('user')->id()) {
            return $this->user2;
        } else {
            return $this->user1;
        }
    }
}
