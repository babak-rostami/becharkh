<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAdvertisePackage extends Model
{
    use HasFactory;

    public function package()
    {
        return $this->hasOne(AdvertisePackage::class, 'id','package_id');
    }

}
