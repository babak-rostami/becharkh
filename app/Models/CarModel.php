<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'model_id')->orderBy('created_at', 'desc');
    }

    public function details()
    {
        return $this->hasMany(ModelDatail::class, 'model_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(CarModelComment::class, 'model_id')->where('parent_id', null)->orderBy('id', 'desc');
    }

    public function shortlink()
    {
        return $this->hasOne(ShortLink::class, 'link_id')->where('link_class', 'car_page');
    }

    public function years()
    {
        return $this->hasMany(CarTrimYear::class, 'model_id');
    }

    public function trims()
    {
        return $this->hasMany(CarTrim::class, 'model_id');
    }

    public function advocates()
    {
        return $this->hasMany(CarAdvocate::class, 'model_id');
    }

    public function allSendedUser()
    {
        return $this->belongsToMany(User::class, 'car_model_comments', 'model_id', 'user_id');
    }

    public function usersSendedMessage()
    {
        return $this->allSendedUser->unique();
    }

    public function userOrderByComment()
    {
        $users = $this->usersSendedMessage()->sortByDesc(function ($user) {
            return $user->oneCarComments($this)->count();
        });
        return $users;

    }

    public function allImages()
    {
        return $this->hasMany(CarImages::class, 'model_id');
    }

    public function acceptedImages()
    {
        return $this->hasMany(CarImages::class, 'model_id')->where('status', 1)->get();
    }

}
