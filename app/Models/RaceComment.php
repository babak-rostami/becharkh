<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaceComment extends Model
{
    use HasFactory;

    public function replies()
    {
        return $this->hasMany(RaceComment::class, 'parent_id');
    }

    public function replyto()
    {
        return $this->belongsTo(RaceComment::class, 'reply_to_id');
    }

    public function race()
    {
        return $this->belongsTo(Race::class, 'race_id');
    }

}
