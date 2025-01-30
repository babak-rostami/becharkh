<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shahr extends Model
{
    use HasFactory;

    protected $table = 'shahrestan';

    public function ostan()
    {
        return $this->belongsTo(Ostan::class, 'ostan_id');
    }

    public function advertises()
    {
        return $this->hasMany(Advertise::class, 'city');
    }


}
