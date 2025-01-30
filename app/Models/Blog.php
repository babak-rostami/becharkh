<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(SiteCategory::class, 'category_id', 'id');
    }

    public function featureValues()
    {
        return $this->hasMany(BlogFeatureValue::class, 'blog_id');
    }

    public function issetFeature($feature_id)
    {
        if ($feature_id == null) {
            return false;
        }
        foreach ($this->featureValues as $fv) {
            if ($fv->feature_id == $feature_id && $fv->item_id != null && $fv->item_id != "-1") {
                return true;
            }
        }
        return false;
    }

    public function getFeatureValue($feature_id)
    {
        foreach ($this->featureValues as $fv) {
            if ($fv->feature_id == $feature_id) {
                return $fv->item_id;
            }
        }
        return null;
    }

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
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->thum;
        } else {
            return 'files/other/images/blog1.png';
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function userImage()
    {
        if (isset($this->user)) {
            return $this->user->image();
        } else {
            return 'files/other/images/profile.jpg';
        }
    }

    public function likeAndUnlikes()
    {
        return $this->hasMany(BlogLike::class, 'blog_id');
    }
    public function likes()
    {
        return $this->hasMany(BlogLike::class, 'blog_id')->where('like_or_unlike', 1);
    }
    public function unlikes()
    {
        return $this->hasMany(BlogLike::class, 'blog_id')->where('like_or_unlike', 0);
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id')->where('parent_id', null)->orderBy('id', 'desc');
    }

    public function shortlink()
    {
        return $this->hasOne(ShortLink::class, 'link_id')->where('link_class', 'blog');
    }

    public function commentCount()
    {
        return $this->hasMany(BlogComment::class, 'blog_id')->count();
    }

    public function video()
    {
        $bv = $this->blogVideo;
        if (isset($bv)) {
            return $bv->video;
        } else {
            return null;
        }
    }

    public function blogVideo()
    {
        return $this->hasOne(BlogVideo2::class, 'blog_id', 'id');
    }

    public function hasVideo()
    {
        if (isset($this->blogVideo)) {
            return true;
        } else {
            return false;
        }
    }
}
