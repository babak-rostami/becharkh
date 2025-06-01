<?php

namespace App\Models;

use App\Services\Elasticsearch;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoQuestion extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'questions';

    public static $elasticIndexName = 'questions';
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

    public function scopeRandom($query, $limit = 15, $filter = null)
    {
        return $query->raw(function ($collection) use ($limit, $filter) {
            $pipeline = [];
            // Add the match stage only if a filter is provided
            if (!is_null($filter) && !empty($filter)) {
                $pipeline[] = ['$match' => $filter];
            }

            // Add the sample stage
            $pipeline[] = ['$sample' => ['size' => $limit]];

            return $collection->aggregate($pipeline);
        });
    }

    public function category()
    {
        return $this->belongsTo(MongoCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(MongoQuestionAnswer::class, 'question_id')->whereNull('parent_id');
    }

    public function likes()
    {
        return $this->hasMany(MongoQuestionLike::class, 'question_id');
    }

    public function getItems()
    {
        if (!isset($this->items)) {
            return collect();
        }
        return MongoItem::whereIn('_id', $this->items)->get();
    }


    public function videoPath()
    {
        $videoPath = $this->video_path;
        if (isset($videoPath)) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $videoPath;
        } else {
            return null;
        }
    }

    public function video()
    {
        return $this->belongsTo(MongoVideo::class, 'video_id');
    }

    public function nacVideo()
    {
        return $this->belongsTo(MongoVideo::class, 'nac_videos');
    }

    public function image()
    {
        if (isset($this->attributes['image'])) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->attributes['image'];
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
}
