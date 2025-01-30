<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertiseVideo extends Model
{
    use HasFactory;

    public function advertise()
    {
        return $this->hasOne(Advertise::class, 'id', 'ad_id');
    }

    public function video()
    {
        return $this->hasOne(Video::class, 'id', 'video_id');
    }
}
