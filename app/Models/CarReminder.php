<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarReminder extends Model
{
    use HasFactory;

    public function brand()
    {
        return $this->hasOne(Brand::class,'id','brand_id');
    }

    public function model()
    {
        return $this->hasOne(CarModel::class,'id','model_id');
    }

}
