<?php

namespace App\Models;

use App\Services\Elasticsearch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoItem extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'items';

    protected $fillable = [
        'feature_id',
        'parent_id',
        'title',
        'title_en',
        'slug',
        'similar_search',
        'status',
        'images'
    ];

    public static $elasticIndexName = 'items';
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
            if ($model->feature->is_in_filter_rtable == 1) {
                $elasticsearch = new Elasticsearch();
                $elasticsearch->createDocument(static::$elasticIndexName, $model->id, [
                    'similar_search' => $model->similar_search,
                ]);
            }
        });

        static::updated(function ($model) {
            if ($model->feature->is_in_filter_rtable == 1) {
                $dirtyAttributes = $model->getDirty();
                if (array_intersect(['similar_search'], array_keys($dirtyAttributes))) {
                    $elasticsearch = new Elasticsearch();
                    $elasticsearch->updateDocument(static::$elasticIndexName, $model->id, [
                        'similar_search' => $model->similar_search
                    ]);
                }
            }
        });

        static::deleted(function ($model) {
            if ($model->feature->is_in_filter_rtable == 1) {
                $elasticsearch = new Elasticsearch();
                $elasticsearch->deleteDocument(static::$elasticIndexName, $model->id);
            }
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

    public function parent()
    {
        return $this->belongsTo(MongoItem::class, 'parent_id');
    }

    public function category()
    {
        return $this->belongsTo(MongoCategory::class, 'category_id');
    }

    public function feature()
    {
        return $this->belongsTo(MongoFeature::class, 'feature_id');
    }

    public function parents()
    {
        $item = $this;
        $parentItems = collect();

        while ($item->parent_id) {
            $parent = $item->parent;
            $parentItems->push($parent);
            $item = $parent;
        }

        return $parentItems;
    }

    public function image($key = 0)
    {
        if (isset($this->images[$key])) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->images[$key];
        } else {
            return $this->category->image();
        }
    }

    public function thumb()
    {
        if (isset($this->images[0])) {
            $t = explode('.webp', $this->images[0])[0] . '2.webp';
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $t;
        } else {
            return $this->category->thumb();
        }
    }

    public function withParentsCommentUrl()
    {
        $baseurl = 'https://becharkh.com/forum';
        if (isset($this->with_parent_url)) {
            $comment_url = $baseurl . $this->with_parent_url;
            return $comment_url;
        }
        return null;
    }

    public function withParentsForumUrl()
    {
        $comment_url = $this->withParentsCommentUrl();
        if ($comment_url != null) {
            return str_replace('s=1&', '', $comment_url);
        }
        return null;
    }

    public function withParentsAdvertiseUrl($category_slug = null)
    {
        $forum_url = $this->withParentsForumUrl();
        if ($forum_url != null) {
            if (isset($category_slug)) {
                $item_cat = $this->category;
                $forum_url = preg_replace('/' . preg_quote($item_cat->slug, '/') . '/', $category_slug, $forum_url, 1);
            }
            return str_replace('forum', 'ads', $forum_url);
        }
        return null;
    }

    public function withParentsBlogUrl()
    {
        $forum_url = $this->withParentsForumUrl();
        if ($forum_url != null) {
            return str_replace('forum', 'blogs', $forum_url);
        }
        return null;
    }

    public function canUpdatePrice()
    {
        $prices = $this->prices ?? [];
        if (count($prices) == 0) {
            return 1;
        } else {
            if (isset($prices['last_update'])) {
                $last_update = $prices['last_update'];
                if (now()->timestamp - $last_update < 21600) {
                    return 0;
                } else {
                    return 1;
                }
            }
        }
    }

    public function advertises($take = null, $order = null)
    {
        $query = MongoAdvertise::where('items', $this->id)->where('status', 1);
        if ($order) {
            $query->orderBy($order, 'desc');
        }
        if ($take) {
            $query->take($take);
        }
        return $query->get();
    }


    public function comments($take = null, $order = null)
    {
        $query = MongoCategoryComment::where('items', $this->id)->where('status', 1);
        if ($order) {
            $query->orderBy($order, 'desc');
        }
        if ($take) {
            $query->take($take);
        }
        return $query->get();
    }

    public function getItems()
    {
        if (!isset($this->items)) {
            return collect();
        }
        $items = MongoItem::where('status', 1)->get();
        return $items->whereIn('_id', $this->items);
    }

    public function withParentsTitle()
    {
        return $this->getparentTitle($this);
    }

    private function getparentTitle($item)
    {
        $title = null;
        $features = MongoFeature::where('status', 1)->get();

        $feature = $features->find($item->feature_id);
        $parentItem = MongoItem::find($item->parent_id);
        if (isset($parentItem)) {
            $parentItemFeature = $features->find($parentItem->feature_id);
        }
        if (isset($parentItem) && isset($parentItemFeature) && $parentItemFeature->item_is_in_title && !$parentItemFeature->item_is_in_title_if_not_parent) {
            if ($feature->is_feature_in_title) {
                $title .= $this->getparentTitle($parentItem) . " " . $feature->title . " " . $item->title;
            } else {
                $title .= $this->getparentTitle($parentItem) . " " . $item->title;
            }
        } else {
            if ($feature->is_feature_in_title) {
                $title = $feature->title . " " . $item->title;
            } else {
                $title = $item->title;
            }
        }
        return $title;
    }
}
