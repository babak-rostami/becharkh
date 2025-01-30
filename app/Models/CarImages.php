<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarImages extends Model
{
    use HasFactory;

    public function model()
    {
        return $this->belongsTo(CarModel::class,'model_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}
