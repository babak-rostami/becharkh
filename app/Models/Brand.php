<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    public function models()
    {
        return $this->hasMany(CarModel::class, 'brand_id');
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'brand_id')->orderBy('created_at', 'desc');
    }

    public function findIt($nameEn)
    {
        return Brand::where('nameEn', $nameEn)->first();
    }

    public function findItId($id)
    {
        return Brand::find($id);
    }

    public function countModelsHasDetail()
    {
        $count = 0;
        foreach ($this->models as $model) {
            if (isset($model->detail)) {
                $count += 1;
            }
        }
        return $count;
    }

    public function details()
    {
        return $this->hasMany(ModelDatail::class,'brand_id','id');
    }

}
