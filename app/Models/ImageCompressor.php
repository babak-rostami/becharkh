<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageCompressor extends Model
{
    use HasFactory;

    public function image()
    {
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->image;
        } else {
            return 'files/other/ic-upload.jpg';
        }
    }
    public function newImage()
    {
        if ($this->compress_image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->compress_image;
        } else {
            return 'files/other/ic-upload.jpg';
        }
    }
}
