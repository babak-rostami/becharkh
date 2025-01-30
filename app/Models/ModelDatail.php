<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelDatail extends Model
{
    use HasFactory;

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id', 'id');
    }


    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function shortlink()
    {
        return $this->hasOne(ShortLink::class, 'link_id')->where('link_class', 'detail');
    }

    public function comments()
    {
        return $this->hasMany(DetailComment::class, 'detail_id')->where('parent_id', null);
    }

    public function key()
    {
        foreach ($this->model->details as $key => $detail) {
            if ($this->id == $detail->id) {
                return $key;
            }
        }
    }

}
