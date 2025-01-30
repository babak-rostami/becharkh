<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogFeatureValue extends Model
{
    use HasFactory;

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function item(){
        return $this->belongsTo(CategoryFeatureItem::class , 'item_id');
    }

}
