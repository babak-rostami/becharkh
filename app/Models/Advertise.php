<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// status = 1 mean ad accepted
// status = 0 mean ad not accepted because of category or item not accepted or ad is not accepted for a reason
// status = 2 mean ad not accepted because waiting for user pay for it
// status = 3 mean ad not accepted because waiting for accept user email
// status = 4 mean ad not accepted because waiting for accept user email and pay for it
class Advertise extends Model
{
    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(SiteCategory::class, 'category_id');
    }

    public function issetFeature($feature_id)
    {
        if ($feature_id == null) {
            return false;
        }
        foreach ($this->featuresValue as $fv) {
            if ($fv->feature_id == $feature_id && $fv->value != null && $fv->value != "-1") {
                return true;
            }
        }
        return false;
    }

    public function getFeatureValue($feature_id)
    {
        foreach ($this->featuresValue as $fv) {
            if ($fv->feature_id == $feature_id) {
                return $fv->value;
            }
        }
        return null;
    }

    public function getFeatureValueTitle($feature_id)
    {
        foreach ($this->featuresValue as $fv) {
            if ($fv->feature_id == $feature_id) {
                $feature = CategoryFeature::find($feature_id);
                if ($feature->items->count() > 0) {
                    if ($fv->value != null && $feature->items->where('id', $fv->value)->first() != null) {
                        return $feature->items->where('id', $fv->value)->first()->title;
                    } else {
                        return "-";
                    }
                } else {
                    return $fv->value;
                }
            }
        }
        return null;
    }


    public function itemsByFilterParent($feature_id)
    {
        $feature = CategoryFeature::find($feature_id);
        if ($this->issetFeature($feature->parent_id)) {
            $pfv = $this->featuresValue->where('feature_id', $feature->parent_id)->first();
            return $feature->items->where('parent_id', $pfv->value);
        } else {
            return $feature->items;
        }
    }

    public function allImages()
    {
        return $this->hasMany(AdvertiseImage::class, 'advertise_id');
    }

    public function images()
    {
        return $this->hasMany(AdvertiseImage::class, 'advertise_id')->where('thum', 0);
    }

    public function thumbnail()
    {
        return $this->allImages->where('thum', 1)->first();
    }

    public function existImages()
    {
        return $this->hasMany(AdvertiseImage::class, 'advertise_id')->where('image', '!=', null);
    }

    public function image()
    {
        $thumb = $this->thumbnail();
        if (isset($thumb)) {
            return $thumb->showImageFromFTP();
        } else {
            if ($this->images->count() > 0) {
                foreach ($this->images as $i) {
                    if ($i->image != null) {
                        $path = "https://dl.becharkh.com/user_files/";
                        return $path . $i->image;
                    }
                }
            } else {
                return asset("files/other/images/default.jpg");
            }
        }
    }


    public function ostann()
    {
        return $this->hasOne(Ostan::class, 'id', 'ostan');
    }

    public function shahrr()
    {
        return $this->hasOne(Shahr::class, 'id', 'city');
    }

    public function showPrice()
    {
        if ($this->price == null) {
            return "توافقی";
        } else {
            return $this->price . " تومان ";
        }
    }

    public function ips()
    {
        return $this->hasMany(AdvertiseViewCount::class, 'advertise_id');
    }

    public function SaveIp($ip)
    {
        $count = 0;
        foreach ($this->ips as $i) {
            if ($i->ip == $ip) {
                $count++;
                break;
            }
        }
        if ($count == 0) {
            $ad = new AdvertiseViewCount();
            $ad->ip = $ip;
            $ad->advertise_id = $this->id;
            $ad->save();
        }
    }

    public function comments()
    {
        return $this->hasMany(AdvertiseComment::class, 'advertise_id')->where('parent_id', null)->orderBy('id', 'desc');
    }


    public function featureValues()
    {
        return $this->hasMany(AdvertiseFeatureValue::class, 'advertise_id');
    }
    
    public function featuresValue()
    {
        return $this->hasMany(AdvertiseFeatureValue::class, 'advertise_id');
    }

    public function reletadAds()
    {
        $ads = collect();
        foreach ($this->featuresValue as $afv) {
            if ($afv->feature->is_in_filter_ad) {
                $afvs = AdvertiseFeatureValue::where('feature_id', $afv->feature_id)
                    ->where('value', $this->getFeatureValue($afv->feature_id))->get();
            }
            foreach ($afvs as $a) {
                $ads->add($a->advertise);
            }
        }
        if ($ads->isEmpty()) {
            $ads = $this->category->advertises;
        }
        $ads = $ads->unique()->take(10);
        return $ads;
    }

    public function advertiseVideo()
    {
        return $this->hasOne(AdvertiseVideo::class, 'ad_id', 'id');
    }

    public function video()
    {
        $av = $this->advertiseVideo;
        if (isset($av)) {
            return $av->video;
        } else {
            return null;
        }
    }
}
