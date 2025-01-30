<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModelComment extends Model
{
    use HasFactory;


    public function replies()
    {
        return $this->hasMany(CarModelComment::class, 'parent_id');
    }

    public function replyto()
    {
        return $this->belongsTo(CarModelComment::class, 'reply_to_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }


    public function trim()
    {
        return $this->hasOne(CarTrim::class,'id','trim_id');
    }

    public function category()
    {
        if ($this->car_topic_category == 1 || $this->car_topic_category == null) {
            return "نظرات کلی";
        } elseif ($this->car_topic_category == 2) {
            return "مقایسه با رقبا";
        } elseif ($this->car_topic_category == 3) {
            return "مشکلات و معایب";
        } elseif ($this->car_topic_category == 4) {
            return "نقد و بررسی";
        } elseif ($this->car_topic_category == 5) {
            return "تصاویر ارسالی کاربران";
        }
    }


    public function likes()
    {
        return $this->hasMany(CarModelCommentLike::class, 'model_comment_id')->where('like_or_unlike', true)->get();
    }

    public function unLikes()
    {
        return $this->hasMany(CarModelCommentLike::class, 'model_comment_id')->where('like_or_unlike', false)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function name()
    {
        if (isset($this->user_id)) {
            return $this->user->username;
        } else {
            return $this->name;
        }
    }

    public function email()
    {
        if (isset($this->user_id)) {
            return $this->user->email;
        } else {
            return $this->email;
        }
    }

    public function image()
    {
        if (isset($this->user_id)) {
            return $this->user->image();
        } else {
            return 'files/other/images/profile.jpg';
        }
    }

}
