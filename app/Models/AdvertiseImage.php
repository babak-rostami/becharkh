<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertiseImage extends Model
{
    use HasFactory;

    public function showImageFromFTP()
    {
        $path = "https://dl.becharkh.com/user_files/";
        return $path . $this->image;
    }
}
