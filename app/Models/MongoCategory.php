<?php

namespace App\Models;

use App\Services\Elasticsearch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoCategory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'categories';

    public static $elasticIndexName = 'categories';
    public static $elasticField = [
        'properties' => [
            'similar_search' => [
                'type' => 'text',
            ]
        ],
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $elasticsearch = new Elasticsearch();
            $elasticsearch->createDocument(static::$elasticIndexName, $model->id, [
                'similar_search' => $model->similar_search
            ]);
        });

        static::updated(function ($model) {
            $dirtyAttributes = $model->getDirty();
            if (array_intersect(['similar_search'], array_keys($dirtyAttributes))) {
                $elasticsearch = new Elasticsearch();
                $elasticsearch->updateDocument(static::$elasticIndexName, $model->id, [
                    'similar_search' => $model->similar_search
                ]);
            }
        });

        static::deleted(function ($model) {
            $elasticsearch = new Elasticsearch();
            $elasticsearch->deleteDocument(static::$elasticIndexName, $model->id);
        });
    }

    public static function elSearch($search)
    {
        $client = new Elasticsearch();
        $response = $client->search(static::$elasticIndexName, $search);
        $ids = array_map(function ($hit) {
            return $hit['_id'];
        }, $response);
        return $ids;
    }

    public function children()
    {
        return $this->hasMany(MongoCategory::class, 'parent_id');
    }

    public function allChildren()
    {
        $children = collect();
        $this->load('children');
        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->allChildren());
        }
        return $children;
    }

    public function relatedCategories()
    {
        $relatedCats = $this->related_cats;

        $categories = MongoCategory::whereIn('_id', $relatedCats)->get();

        $categories = $categories->sortBy(function ($category) use ($relatedCats) {
            return array_search($category->_id, $relatedCats);
        });

        return $categories->values();
    }

    public function features()
    {
        $feature_ids = $this->feature_ids ?? [];
        return MongoFeature::whereIn('_id', $feature_ids)->get();
    }

    public function allFeaturesWithParentsFeaturesForAds()
    {
        $parents = $this->parents();
        $category = $this;
        $allfeatures = $category->features()->where('is_in_filter_ad', 1)->where('is_in_filter_ad', 1)->where('parent_id', null);
        foreach ($parents as $parent) {
            $allfeatures = $allfeatures->merge($parent->features()->where('is_in_filter_ad', 1)->where('is_in_child_cats', 1)->where('parent_id', null));
        }
        return $allfeatures;
    }

    public function parent()
    {
        return $this->belongsTo(MongoCategory::class, 'parent_id');
    }

    public function parents()
    {
        $category = $this;
        $parentCategories = collect();
        while ($category->parent_id) {
            $parent = MongoCategory::find($category->parent_id);
            $parentCategories->push($parent);
            $category = $parent;
        }
        return $parentCategories;
    }

    public function canDelete()
    {
        if (
            $this->status == 0 &&
            $this->advertises->isEmpty() &&
            $this->questions->isEmpty() &&
            $this->children->isEmpty() &&
            $this->comments->isEmpty() &&
            $this->blogs->isEmpty()
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function comments()
    {
        return $this->hasMany(MongoCategoryComment::class, 'category_id')->orderBy('created_at', 'desc');
    }

    public function commentsWithUserAndTake($is_parent_null, $take)
    {
        if ($is_parent_null) {
            return $this->hasMany(MongoCategoryComment::class, 'category_id')->whereNull('parent_id')->with('user')->take($take)->get();
        } else {
            return $this->hasMany(MongoCategoryComment::class, 'category_id')->with('user')->take($take)->get();
        }
    }

    public function questions()
    {
        return $this->hasMany(MongoQuestion::class, 'category_id')->orderBy('created_at', 'desc');
    }

    public function advertises()
    {
        return $this->hasMany(MongoAdvertise::class, 'category_id')->orderBy('created_at', 'desc');
    }

    public function blogs()
    {
        return $this->hasMany(MongoBlog::class, 'category_id')->orderBy('created_at', 'desc');
    }

    public function getImage()
    {
        return $this->attributes['image'];
    }

    public function image()
    {
        $image = $this->attributes['image'];
        if (isset($image)) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $image;
        } else {
            return 'https://dl.becharkh.com/user_files/files/other/images/default.jpg';
        }
    }

    public function thumb()
    {
        $image = $this->attributes['image'];
        if (isset($image)) {
            $path = "https://dl.becharkh.com/user_files/";
            $t = explode('.webp', $image)[0] . '2.webp';
            return $path . $t;
        } else {
            return 'https://dl.becharkh.com/user_files/files/other/images/default.jpg';
        }
    }

    public function newQuestionUrl($category_slug = null)
    {
        $req = Request::getRequestUri();
        $allQueryarr = explode('?', $req);

        if (isset($allQueryarr[1])) {

            if (count($allQueryarr) > 2) {
                foreach ($allQueryarr as $key => $aq) {
                    if ($key > 1) {
                        $allQueryarr[1] .= $aq;
                    }
                }
            }
            while (substr($allQueryarr[1], 0, 1) === "?" || substr($allQueryarr[1], 0, 1) === "&") {
                $allQueryarr[1] = substr($allQueryarr[1], 1);
            }
            while (
                strpos($allQueryarr[1], '&&') !== false || strpos($allQueryarr[1], '?&') !== false
                || strpos($allQueryarr[1], '&?') !== false || strpos($allQueryarr[1], '??') !== false
            ) {
                $allQueryarr[1] = str_replace('??', '?', $allQueryarr[1]);
                $allQueryarr[1] = str_replace('&&', '&', $allQueryarr[1]);
                $allQueryarr[1] = str_replace('?&', '&', $allQueryarr[1]);
                $allQueryarr[1] = str_replace('&?', '&', $allQueryarr[1]);
            }

            $allQuery = $allQueryarr[1];
        }
        if (isset($allQuery)) {
            return route('question.create', $category_slug) . "?" . $allQuery;
        } else {
            return route('question.create', $category_slug);
        }
    }

    public function newAdvertiseUrl($category_slug = null)
    {
        $req = Request::getRequestUri();
        $allQueryarr = explode('?', $req);
        if (isset($allQueryarr[1])) {

            if (count($allQueryarr) > 2) {
                foreach ($allQueryarr as $key => $aq) {
                    if ($key > 1) {
                        $allQueryarr[1] .= $aq;
                    }
                }
            }
            while (substr($allQueryarr[1], 0, 1) === "?" || substr($allQueryarr[1], 0, 1) === "&") {
                $allQueryarr[1] = substr($allQueryarr[1], 1);
            }
            while (
                strpos($allQueryarr[1], '&&') !== false || strpos($allQueryarr[1], '?&') !== false
                || strpos($allQueryarr[1], '&?') !== false || strpos($allQueryarr[1], '??') !== false
            ) {
                $allQueryarr[1] = str_replace('??', '?', $allQueryarr[1]);
                $allQueryarr[1] = str_replace('&&', '&', $allQueryarr[1]);
                $allQueryarr[1] = str_replace('?&', '&', $allQueryarr[1]);
                $allQueryarr[1] = str_replace('&?', '&', $allQueryarr[1]);
            }

            $allQuery = $allQueryarr[1];
        }
        if (isset($allQuery)) {
            return route('new.ad', $category_slug) . "?" . $allQuery;
        } else {
            return route('new.ad', $category_slug);
        }
    }
}
