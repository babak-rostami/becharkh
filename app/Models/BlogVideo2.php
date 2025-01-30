<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogVideo2 extends Model
{
    use HasFactory;

    public function video()
    {
        return $this->hasOne(Video::class, 'id', 'video_id');
    }
}
