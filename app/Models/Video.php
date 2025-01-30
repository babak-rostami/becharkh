<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// video status
// 0 mean not accepted video that upload in ftp
// 1 mean accepted video that upload in ftp
// 2 mean not accepted video from youtube
// 3 mean accepted video from youtube
class Video extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(SiteCategory::class, 'category_id', 'id');
    }

    public function featureValues()
    {
        return $this->hasMany(VideoFeatureValue::class, 'video_id');
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

    public function videoPath()
    {
        $videoPath = $this->video_path;
        if ($videoPath != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $videoPath;
        } else {
            return null;
        }
    }

    public function hasVideoFile()
    {
        $videoPath = $this->video_path;
        if ($videoPath != null) {
            return true;
        } else {
            return false;
        }
    }

    public function isVideoFromYoutue()
    {
        if ($this->status == 2 || $this->status == 3) {
            return true;
        } else {
            return false;
        }
    }

    public function yformats()
    {
        return $this->hasMany(VideoYoutubeFormat::class, 'video_id');
    }

    public function findYVFormat($format_id)
    {
        $format = $this->yformats->where('video_format', $format_id)->first();
        if (isset($format)) {
            return $format;
        } else {
            return $this->defaultYVFormat();
        }
    }

    public function defaultYVFormat($format_id = null)
    {
        $formats = $this->yformats;
        if ($format_id == null) {
            foreach ($formats as $f) {
                if ($f->video_format == "720P") {
                    return $f;
                }
            }
            return $f;
        } else {
            $format = $formats->find($format_id);
            if (isset($format)) {
                return $format;
            } else {
                return $this->defaultYVFormat();
            }
        }
    }

    public function likeAndUnlikes()
    {
        return $this->hasMany(VideoLike::class, 'video_id');
    }
    public function likes()
    {
        return $this->hasMany(VideoLike::class, 'video_id')->where('like_or_unlike', 1);
    }
    public function unlikes()
    {
        return $this->hasMany(VideoLike::class, 'video_id')->where('like_or_unlike', 0);
    }

    public function allComments()
    {
        return $this->hasMany(VideoComment::class, 'video_id');
    }
    public function parentComments()
    {
        return $this->hasMany(VideoComment::class, 'video_id')->where('parent_id', null)->orderBy('id', 'desc');
    }
    public function allCommentCount()
    {
        return $this->allComments->count();
    }

    public function hotComment()
    {
        $pComs = $this->parentComments;
        if (count($pComs) > 0) {
            $unlikes =  VideoComment::where('video_id', $this->id)->whereHas('unLikes')->withCount('unLikes')->orderBy('un_likes_count', 'desc')->get();
            if (count($unlikes) > 0) {
                return $unlikes->first();
            }
            $likes =  VideoComment::where('video_id', $this->id)->whereHas('likes')->withCount('likes')->orderBy('likes_count', 'desc')->get();
            if (count($likes) > 0) {
                return $likes->first();
            }
            return $pComs->first();
        } else {
            return null;
        }
    }

    public function videoAdvertises()
    {
        return $this->hasMany(AdvertiseVideo::class, 'video_id');
    }
    public function advertise()
    {
        $ads = $this->videoAdvertises;
        if (count($ads) > 0) {
            $ad = $ads->first()->advertise;
            if (isset($ad)) {
                return $ad;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
