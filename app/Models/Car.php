<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function model()
    {
        return $this->hasOne(CarModel::class, 'id', 'model_id');
    }

    public function advertise($id, $class)
    {
        return Advertise::where('ad_id', $id)->where('ad_class', $class)->first();
    }

}
