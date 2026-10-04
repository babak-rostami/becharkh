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
}
