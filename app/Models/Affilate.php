<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class Affilate extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'affilates';

    public function videoPath()
    {
        $videoPath = $this->video_path;
        if (isset($videoPath)) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $videoPath;
        } else {
            return null;
        }
    }

    public function video()
    {
        return $this->belongsTo(MongoVideo::class, 'video_id');
    }

    public function comments()
    {
        return $this->hasMany(ProductComment::class, 'product_id')->whereNull('parent_id')->orderBy('created_at', 'desc');
    }
}
