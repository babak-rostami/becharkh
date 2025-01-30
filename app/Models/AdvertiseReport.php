<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertiseReport extends Model
{
    use HasFactory;

    public function advertise()
    {
        return $this->hasOne(Advertise::class,'id','advertise_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

}
