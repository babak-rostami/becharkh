<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCkImage extends Model
{
    use HasFactory;

    protected $table = 'user_ck_images';

    public function image()
    {
        if ($this->url != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->url;
        } else {
            return 'files/other/images/blog1.png';
        }
    }
}
