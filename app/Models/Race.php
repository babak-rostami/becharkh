<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Race extends Model
{
    use HasFactory;

    public function user()
    {
        if ($this->creator_class == 'admin') {
            return $this->belongsTo(Admin::class, 'creator_id');
        } elseif ($this->creator_class == 'user') {
            return $this->belongsTo(User::class, 'creator_id');
        }
    }

    public function creatorToString()
    {
        if ($this->creator_class == 'admin') {
            return 'ادمین';
        } elseif ($this->creator_class == 'user') {
            return 'عضو سایت';
        }
    }

    public function untilEndHour()
    {
        if (\Carbon\Carbon::parse($this->end_time)->greaterThan(Carbon::now())) {
            return \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($this->end_time));
        } else {
            return '-';
        }
    }

    public function untilEndMin()
    {
        if (\Carbon\Carbon::parse($this->end_time)->greaterThan(Carbon::now())) {
            return \Carbon\Carbon::now()->diffInMinutes(\Carbon\Carbon::parse($this->end_time));
        } else {
            return '-';
        }
    }

    public function untilEndSec()
    {
        if (\Carbon\Carbon::parse($this->end_time)->greaterThan(Carbon::now())) {
            return \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($this->end_time));
        } else {
            return '-';
        }
    }


    public function options()
    {
        return $this->hasMany(RaceOption::class, 'race_id');
    }

    public function comments()
    {
        return $this->hasMany(RaceComment::class, 'race_id')->where('parent_id', null)->orderBy('id', 'desc');;
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_pages', 'page_id', 'tag_id')->where('page_class', 'race')->get();
    }

    public function voteCount()
    {
        $count = 0;
        foreach ($this->options as $op) {
            $count += $op->votes->count();
        }
        return $count;
    }

}
