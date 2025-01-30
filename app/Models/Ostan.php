<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ostan extends Model
{
    use HasFactory;

    protected $table = 'ostan';

    public function cities()
    {
        return $this->hasMany(Shahr::class, 'ostan_id');
    }

    public function findIt($id)
    {
        return Ostan::find($id);
    }


    public function advertises()
    {
        return $this->hasMany(Advertise::class, 'ostan');
    }

//    آیا خودرو با برند داه شده در این استان است؟
    public function hasBrand($brand)
    {
        foreach ($this->advertises as $advertise) {
            if ($advertise->adModel->brand->nameEn == $brand) {
                return true;
            }
        }
        return false;
    }

//    آیا خودرو با مدل داه شده در این استان است؟
    public function hasModel($model)
    {
        foreach ($this->advertises as $advertise) {
            if ($advertise->adModel->model->nameEn == $model) {
                return true;
            }
        }
        return false;
    }


}
