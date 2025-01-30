<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(SiteCategory::class, 'category_id');
    }
    public function feature()
    {
        return $this->belongsTo(CategoryFeature::class, 'feature_id');
    }
    public function item()
    {
        return $this->belongsTo(CategoryFeatureItem::class, 'item_id');
    }

    public function rewards()
    {
        return $this->hasMany(MissionReward::class, 'mission_id');
    }

    public function planTime()
    {
        if (jdate($this->expire_time)->greaterThanCarbon(Carbon::now())) {
            $day = \Illuminate\Support\Carbon::now()->diffInDays($this->expire_time);
            $hour = \Illuminate\Support\Carbon::now()->diffInHours($this->expire_time) - ($day * 24);
            $min = \Illuminate\Support\Carbon::now()->diffInMinutes($this->expire_time) - (($day * 1440) + ($hour * 60));
            $sec = \Illuminate\Support\Carbon::now()->diffInSeconds($this->expire_time) - (($day * 86400) + ($hour * 3600) + ($min * 60));
            return response()->json([
                'day' => $day,
                'hour' => $hour,
                'min' => $min,
                'sec' => $sec,
            ], 200);
        } else {
            return response()->json([
                'day' => '0',
                'hour' => '0',
                'min' => '0',
                'sec' => '0',
            ], 200);
        }
    }
}
