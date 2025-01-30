<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoYoutubeFormat extends Model
{
    use HasFactory;

    protected $table = 'video_youtube_formats';

    public function videoPath()
    {
        $videoPath = $this->file_path;
        if ($videoPath != null) {
            if (strpos($videoPath, 'files/yfiles/') === 0) {
                return asset($this->file_path);
            } else {
                $path = "https://dl.becharkh.com/user_files/";
                return $path . $this->file_path;
            }
        } else {
            return null;
        }
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
}
