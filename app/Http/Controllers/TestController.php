<?php

namespace App\Http\Controllers;

use App\Models\Affilate;
use App\Models\MongoAdvertise;
use App\Models\MongoBlog;
use App\Models\MongoBlogLike;
use App\Models\MongoCategory;
use App\Models\MongoCategoryComment;
use App\Models\MongoCategoryCommentLike;
use App\Models\MongoFeature;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoQuestionLike;
use App\Models\MongoUser;
use App\Models\MongoUserMedal;
use App\Models\MongoVideo;
use App\Models\MongoVideoComment;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function start()
    {

        // $this->createIndexes();

        // $advertises = MongoAdvertise::all();
        // foreach ($advertises as $advertise) {
        //     if ($advertise->status == 1) {
        //         $this->addAdvertiseToItem($advertise);
        //     }
        // }
    }

    private function addAdvertiseToItem($advertise)
    {
        $items = $advertise->getItems();
        foreach ($items as $item) {
            $suggest_ads = $item->suggest_ads ?? [];
            if (!in_array($advertise->_id, $suggest_ads)) {
                array_unshift($suggest_ads, $advertise->_id);
            }
            if (count($suggest_ads) > 5) {
                $suggest_ads = array_slice($suggest_ads, 0, 5);
            }
            $item->suggest_ads = $suggest_ads;
            $item->update();
        }
    }


    public function createIndexes()
    {
        ////////////////////////user notifications
        UserNotification::raw(function ($collection) {
            $collection->createIndex([
                'user_id' => 1
            ]);
        });
        UserNotification::raw(function ($collection) {
            $collection->createIndex([
                'type' => 1,
                'type_id' => 1
            ]);
        });
        ////////////////////////user medals
        MongoUserMedal::raw(function ($collection) {
            $collection->createIndex([
                'type' => 1,
                'item_id' => 1,
            ]);
        });
        ////////////////////////user follow items
        MongoFollowItem::raw(function ($collection) {
            $collection->createIndex([
                'user_id' => 1,
                'item_id' => 1,
            ]);
        });
        MongoAdvertise::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        ////////////////////////advertise
        MongoAdvertise::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'category_id' => 1,
                'items' => 1
            ]);
        });
        MongoAdvertise::raw(function ($collection) {
            $collection->createIndex([
                'slug' => 1,
            ]);
        });
        MongoAdvertise::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        ////////////////////////blog
        MongoBlog::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'category_id' => 1,
                'items' => 1
            ]);
        });
        MongoBlog::raw(function ($collection) {
            $collection->createIndex([
                'category_id' => 1,
                'slug' => 1,
                'random_id' => 1
            ]);
        });
        MongoBlog::raw(function ($collection) {
            $collection->createIndex(['title' => 1]);
        });
        MongoBlog::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });
        MongoBlog::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        ////////////////////////blog like
        MongoBlogLike::raw(function ($collection) {
            $collection->createIndex([
                'blog_id' => 1,
                'user_id' => 1
            ]);
        });
        //////////////////////category
        MongoCategory::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });

        //////////////////////category comments
        MongoCategoryComment::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'parent_id' => 1,
                'category_id' => 1,
                'items' => 1
            ]);
        });
        MongoCategoryComment::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'parent_id' => 1,
                'user_id' => 1
            ]);
        });
        //////////////////////category comment likes
        MongoCategoryCommentLike::raw(function ($collection) {
            $collection->createIndex([
                'comment_id' => 1,
                'ip' => 1
            ]);
        });
        //////////////////////features
        MongoFeature::raw(function ($collection) {
            $collection->createIndex(['category_id' => 1]);
        });
        MongoFeature::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });
        //////////////////////items
        MongoItem::raw(function ($collection) {
            $collection->createIndex(['category_id' => 1]);
        });
        MongoItem::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });
        MongoItem::raw(function ($collection) {
            $collection->createIndex([
                'feature_id' => 1,
                'slug' => 1,
            ]);
        });
        //////////////////////question answers
        MongoCategoryComment::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'parent_id' => 1,
                'question_id' => 1,
            ]);
        });
        MongoCategoryComment::raw(function ($collection) {
            $collection->createIndex(['question_id' => 1]);
        });
        //////////////////////question likes
        MongoQuestionLike::raw(function ($collection) {
            $collection->createIndex([
                'question_id' => 1,
                'user_id' => 1
            ]);
        });
        //////////////////////question answer likes
        MongoCategoryCommentLike::raw(function ($collection) {
            $collection->createIndex([
                'comment_id' => 1,
                'ip' => 1
            ]);
        });
        //////////////////////questions
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'category_id' => 1,
                'items' => 1
            ]);
        });
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex(['just_this_page' => 1]);
        });
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex(['slug2' => 1]);
        });
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'user_id' => 1
            ]);
        });
        //////////////////////affilates
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'google_index' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'slug' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'items' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'categories' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'questions' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'video_id' => 1,
                'status' => 1
            ]);
        });
        //////////////////////users
        MongoUser::raw(function ($collection) {
            $collection->createIndex(['username' => 1]);
        });
        MongoUser::raw(function ($collection) {
            $collection->createIndex(['email' => 1]);
        });
        //////////////////////video comments
        MongoVideoComment::raw(function ($collection) {
            $collection->createIndex(['video_id' => 1]);
        });
        //////////////////////videos
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['title' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['random_id' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['category_id' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['items' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['youtube_link' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['created_at' => -1]);
        });
    }
}
