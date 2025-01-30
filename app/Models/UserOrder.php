<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function userPackage()
    {
        return $this->hasOne(UserAdvertisePackage::class, 'order_id');
    }
}
