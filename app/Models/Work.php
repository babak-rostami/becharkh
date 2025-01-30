<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    public function image()
    {
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->image;
        } else {
            return 'files/other/images/default.jpg';
        }
    }

    public function thumb()
    {
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->thum;
        } else {
            return 'files/other/images/default.jpg';
        }
    }
}
