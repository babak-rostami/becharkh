<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarTrim extends Model
{
    use HasFactory;


    public function years(){
        return $this->hasMany(CarTrimYear::class,'trim_id');
    }

}
