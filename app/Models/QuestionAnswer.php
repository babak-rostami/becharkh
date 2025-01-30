<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userImage()
    {
        if (isset($this->user_id)) {
            return $this->user->image();
        } else {
            return 'files/other/images/profile.jpg';
        }
    }

    public function username()
    {
        if (isset($this->user_id)) {
            return $this->user->username;
        } else {
            return $this->name;
        }
    }

    public function name()
    {
        if (isset($this->user_id)) {
            return $this->user->name;
        } else {
            return $this->name . ' (کاربر مهمان) ';
        }
    }

    public function replies()
    {
        return $this->hasMany(QuestionAnswer::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(QuestionAnswerLike::class, 'question_answer_id')->where('like_or_unlike', true)->get();
    }

    public function likes1()
    {
        return $this->hasMany(QuestionAnswerLike::class, 'question_answer_id')->where('like_or_unlike', true);
    }

    public function unLikes()
    {
        return $this->hasMany(QuestionAnswerLike::class, 'question_answer_id')->where('like_or_unlike', false)->get();
    }
    public function unLikes1()
    {
        return $this->hasMany(QuestionAnswerLike::class, 'question_answer_id')->where('like_or_unlike', false);
    }
    public function lukes()
    {
        return $this->hasMany(QuestionAnswerLike::class, 'question_answer_id');
    }
}
