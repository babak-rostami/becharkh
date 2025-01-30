<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    use HasFactory;

    public function item()
    {
        return $this->belongsTo(CategoryFeatureItem::class, 'item_id');
    }

    public function image()
    {
        if (isset($this->image)) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->image;
        } else {
            return 'files/other/images/default.jpg';
        }
    }

    public function thumb()
    {
        if (isset($this->thum)) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->thum;
        } else {
            return 'files/other/images/default.jpg';
        }
    }
}
