<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarTrimYear extends Model
{
    use HasFactory;


    public function trims(){
        return $this->hasMany(CarTrim::class,'id','trim_id');
    }

}
