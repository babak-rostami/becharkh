<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoFeatureValue extends Model
{
    use HasFactory;

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function item()
    {
        return $this->belongsTo(CategoryFeatureItem::class, 'item_id');
    }
}
