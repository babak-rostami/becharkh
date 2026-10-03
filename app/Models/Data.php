<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    public function city($id)
    {
        $city = Shahr::find($id);
        return $city;
    }

    public function ostan($id)
    {
        $ostan = Ostan::find($id);
        return $ostan;
    }
}
