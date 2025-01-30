<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryCommentImage extends Model
{
    use HasFactory;

    public function image()
    {
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->image;
        } else {
            return 'files/other/images/blog1.png';
        }
    }

    public function thumb()
    {
        if ($this->thum != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->thum;
        } else {
            return 'files/other/images/blog1.png';
        }
    }
}
