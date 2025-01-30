<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryComment extends Model
{
    use HasFactory;


    public function image()
    {
        return $this->hasOne(CategoryCommentImage::class, 'comment_id');
    }

    public function imageFile()
    {
        $image = $this->image;
        if (isset($image)) {
            return $image->image();
        } else {
            return 'files/other/images/blog1.png';
        }
    }

    public function featureValues()
    {
        return $this->hasMany(CategoryCommentFeatureValue::class, 'comment_id');
    }

    public function parent()
    {
        return $this->belongsTo(CategoryComment::class, 'parent_id');
    }

    public function featureValuesHasItem()
    {
        return $this->hasMany(CategoryCommentFeatureValue::class, 'comment_id')->where('item_id', '!=', null);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->with('jobs');
    }

    public function userImage()
    {
        if (isset($this->user)) {
            return asset($this->user->image());
        } else {
            return asset('files/other/images/profile.jpg');
        }
    }

    public function category()
    {
        return $this->belongsTo(SiteCategory::class, 'category_id');
    }

    public function lukes()
    {
        return $this->hasMany(CategoryCommentLike::class, 'category_comment_id');
    }

    public function likes()
    {
        return $this->hasMany(CategoryCommentLike::class, 'category_comment_id')->where('like_or_unlike', true)->get();
    }

    public function likes1()
    {
        return $this->hasMany(CategoryCommentLike::class, 'category_comment_id')->where('like_or_unlike', true);
    }

    public function unLikes()
    {
        return $this->hasMany(CategoryCommentLike::class, 'category_comment_id')->where('like_or_unlike', false)->get();
    }
    public function unLikes1()
    {
        return $this->hasMany(CategoryCommentLike::class, 'category_comment_id')->where('like_or_unlike', false);
    }

    public function replies()
    {
        return $this->hasMany(CategoryComment::class, 'parent_id');
    }

    public function replyto()
    {
        return $this->belongsTo(CategoryComment::class, 'reply_to_id');
    }
}
