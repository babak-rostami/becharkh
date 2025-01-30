<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(SiteCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(QuestionLike::class, 'question_id')->where('like_or_unlike', true)->get();
    }

    public function likes1()
    {
        return $this->hasMany(QuestionLike::class, 'question_id')->where('like_or_unlike', true);
    }

    public function unLikes()
    {
        return $this->hasMany(QuestionLike::class, 'question_id')->where('like_or_unlike', false)->get();
    }

    public function unLikes1()
    {
        return $this->hasMany(QuestionLike::class, 'question_id')->where('like_or_unlike', false);
    }


    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id')->where('parent_id', null)->get();
    }
    public function answerss()
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id')->where('parent_id', null);
    }

    public function topAnswer()
    {
        return $this->answers()->sortByDesc('likes1')->first();
    }

    public function wganswers()
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id')->where('parent_id', null);
    }

    public function answerCount()
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id')->count();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_pages', 'page_id', 'tag_id')->where('page_class', 'question')->get();
    }


    public function featureValues()
    {
        return $this->hasMany(QuestionFeatureValue::class, 'question_id');
    }

    public function lastFeatureValueTitle()
    {
        if ($this->featureValues->count() > 0) {
            return $this->featureValues->last()->titleWithParents();
        } else {
            return $this->category->title;
        }
    }

    public function featureValuesHasItem()
    {
        return $this->hasMany(QuestionFeatureValue::class, 'question_id')->where('item_id', '!=', null);
    }

    public function showRoute()
    {
        return route('question.show', ['category' => $this->category->slug, 'slug' => $this->slug, 'random' => $this->random_id]);
    }
}
