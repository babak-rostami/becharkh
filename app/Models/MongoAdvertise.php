<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoAdvertise extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'advertises';

    protected $fillable = [
        'title',
        'slug',
        'random_id',
        'seen_count',
        'body',
        'site_link',
        'ostan',
        'city',
        'price',
        'phone',
        'status',
        'category_id',
        'user_id',
        'items',
        'videos',
        'created_at',
        'updated_at',
    ];

    public function category()
    {
        return $this->belongsTo(MongoCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function getImages()
    {
        $path = "https://dl.becharkh.com/user_files/";
        $images = $this->attributes['images'] ?? [];

        if (!is_null($images) && is_array($images)) {
            return collect($images)->map(function ($filename) use ($path) {
                return [
                    'filename' => $filename,
                    'url' => $path . $filename
                ];
            });
        }

        return collect();
    }


    public function image()
    {
        $images = $this->attributes['images'] ?? null;
        if (isset($images)) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $images[0];
        } else {
            return $this->category->image();
        }
    }

    public function thumbnail()
    {
        $images = $this->attributes['images'] ?? null;
        if (isset($images)) {
            $thumb = explode('.webp', $images[0])[0] . '2.webp';
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $thumb;
        } else {
            return $this->category->image();
        }
    }

    public function getItems()
    {
        if (!isset($this->items)) {
            return collect();
        }
        $items = MongoItem::whereIn('_id', $this->items)->where('status', 1)->get();
        return $items;
    }

    public function video()
    {
        $bv = $this->videos ?? null;
        if ($bv == null) {
            return null;
        } else {
            $video = MongoVideo::find($bv);
            return $video;
        }
    }
    public function nacVideo()
    {
        return $this->belongsTo(MongoVideo::class, 'nac_videos');
    }

    public function reletadAds()
    {
        $ad = $this;
        $items = $ad->items ?? [];
        $ads = collect();
        if (count($items) > 0) {
            $tempAds = MongoAdvertise::whereIn('items', $items)->orderBy('created_at', 'desc')->get();
            if (count($tempAds) > 0) {
                $ads = $ads->merge($tempAds);
            }
        }
        if (count($ads) < 15) {
            $tempAds = MongoAdvertise::where('category_id', $ad->category_id)->take(15 - count($ads))->orderBy('created_at', 'desc')->get();
            if (count($tempAds) > 0) {
                $ads = $ads->merge($tempAds)->unique();
            }
            if (count($ads) < 15) {
                $tempAds = MongoAdvertise::take(15 - count($ads))->orderBy('created_at', 'desc')->get();
                $ads = $ads->merge($tempAds)->unique();
            }
        }
        return $ads->where('id', '!=', $ad->id);
    }

    public function featureValues()
    {
        return $this->hasMany(MongoAdvertiseFeatureValue::class, 'advertise_id');
    }
}
