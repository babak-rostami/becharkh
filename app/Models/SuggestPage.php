<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class SuggestPage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'suggest_pages';

    public function image()
    {
        if (isset($this->attributes['image'])) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->attributes['image'];
        } else {
            return 'files/other/images/blog1.png';
        }
    }

    public function getImage()
    {
        return $this->attributes['image'] ?? null;
    }
}
