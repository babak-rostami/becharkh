<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model
{
    use HasFactory;

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user');
    }

    public function replies()
    {
        return $this->hasMany(UserMessage::class, 'parent')->orderBy('id', 'desc');
    }

    public function unReadInMessage()
    {
        $count = 0;
        if (!$this->seen) {
            if ($this->fromUser->id != auth('user')->id()) {
                $count++;
            }
        }
        foreach ($this->replies as $reply) {
            if ($reply->seen == true) {
                break;
            } else {
                if ($reply->fromUser->id != auth('user')->id()) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function lastMessage()
    {
        if ($this->replies->count() > 0) {
            return $this->replies->first();
        } else {
            return $this;
        }
    }

}
