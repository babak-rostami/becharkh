<?php

namespace App\Models;

use App\Services\Elasticsearch;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Eloquent\Model;

//status 0 mean not accepted because blog category or item not accepted
//status 1 mean accepted
//status 2 mean post temporary saved
class MongoBlog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'blogs';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'seen_count',
        'image',
        'thum',
        'content',
        'short_description',
        'status',
        'user_id',
        'random_id',
        'google_index',
        'like_count',
        'unlike_count',
        'items',
        'created_at',
        'updated_at',
    ];

    public static $elasticIndexName = 'blogs';
    public static $elasticField = [
        'properties' => [
            'title' => [
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
                'title' => $model->title,
            ]);
        });

        static::updated(function ($model) {
            $dirtyAttributes = $model->getDirty();
            if (array_intersect(['title'], array_keys($dirtyAttributes))) {
                $elasticsearch = new Elasticsearch();
                $elasticsearch->updateDocument(static::$elasticIndexName, $model->id, [
                    'title' => $model->title
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

    public function category()
    {
        return $this->belongsTo(MongoCategory::class, 'category_id');
    }

    public function image()
    {
        if (isset($this->attributes['image'])) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->attributes['image'];
        } else {
            return 'files/other/images/blog1.png';
        }
    }

    public function getImage()
    {
        return $this->attributes['image'] ?? null;
    }

    public function thumb()
    {
        if (isset($this->attributes['image'])) {
            $thumb = explode('.webp', $this->attributes['image'])[0] . '2.webp';
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $thumb;
        } else {
            return 'files/other/images/blog1.png';
        }
    }

    public function getItems()
    {
        if (!isset($this->items)) {
            return collect();
        }
        $items = MongoItem::whereIn('_id', $this->items)->where('status', 1)->get();
        return $items;
    }

    public function video()
    {
        $bv = $this->videos ?? null;
        if ($bv == null) {
            return null;
        } else {
            $video = MongoVideo::find($bv);
            return $video;
        }
    }

    public function nacVideo()
    {
        return $this->belongsTo(MongoVideo::class, 'nac_videos');
    }

    public function comments()
    {
        return $this->hasMany(MongoBlogComment::class, 'blog_id')->whereNull('parent_id')->orderBy('created_at', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(MongoBlogLike::class, 'blog_id');
    }

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }
}
