<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoWork extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'works';

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'image',
        'body',
        'similar_search',
        'created_at',
        'updated_at',
    ];


    public function getImage()
    {
        return $this->attributes['image'];
    }

    public function image()
    {
        $image = $this->attributes['image'];
        if (isset($image)) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $image;
        } else {
            return 'https://dl.becharkh.com/user_files/files/other/images/default.jpg';
        }
    }

    public function thumb()
    {
        $image = $this->attributes['image'];
        if (isset($image)) {
            $path = "https://dl.becharkh.com/user_files/";
            $t = explode('.webp', $image)[0] . '2.webp';
            return $path . $t;
        } else {
            return 'https://dl.becharkh.com/user_files/files/other/images/default.jpg';
        }
    }
}
