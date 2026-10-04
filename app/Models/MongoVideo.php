<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class MongoVideo extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'videos';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'video_path',
        'random_id',
        'google_index',
        'status',
        'image',
        'thum',
        'seen_count',
        'category_id',
        'user_id',
        'max_size',
        'youtube_link',
        'like_count',
        'unlike_count',
        'items',
        'created_at',
        'updated_at',
    ];

    // Define a scope for sampling random documents with an optional filter
    public function scopeRandom($query, $limit = 15, $filter = null)
    {
        return $query->raw(function ($collection) use ($limit, $filter) {
            $pipeline = [];
            // Add the match stage only if a filter is provided
            if (!is_null($filter) && !empty($filter)) {
                // Adjust the filter to handle specific conditions
                foreach ($filter as $field => $condition) {
                    if ($condition === 'notnull') {
                        $filter[$field] = ['$exists' => true, '$ne' => null];
                    }
                }
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

    public function advertise()
    {
        if (!isset($this->advertises)) {
            return collect();
        }
        $advertise = MongoAdvertise::find($this->advertises[0]);
        return $advertise;
    }

    public function getItems()
    {
        if (!isset($this->items)) {
            return collect();
        }
        
        return MongoItem::whereIn('_id', $this->items)->get();
    }

    public function isVideoFromYoutue()
    {
        if (isset($this->youtube_link)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function defaultYVFormat($format_id = null)
    {
        $formats = $this->formats;
        return $formats[0];
        // if ($format_id == null) {
        //     foreach ($formats as $f) {
        //         if (Str::lower($f['video_format']) == "360p") {
        //             return $f;
        //         }
        //     }
        //     return $f;
        // } else {
        //     foreach ($formats as $f) {
        //         if ($f['format_id'] == $format_id) {
        //             $format = $f;
        //             break;
        //         }
        //     }
        //     if (isset($format)) {
        //         return $format;
        //     } else {
        //         return $this->defaultYVFormat();
        //     }
        // }
    }

    public function yformatVideoPath()
    {
        $format = $this->defaultYVFormat();
        if (isset($format)) {
            $videoPath = $format['file_path'];
            if ($videoPath != null) {
                if (strpos($videoPath, 'files/yfiles/') === 0) {
                    return asset($videoPath);
                } else {
                    $path = "https://dl.becharkh.com/user_files/";
                    return $path . $videoPath;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function updateFormat($format_id, $video_format = null, $video_size = null, $file_path = null, $filename = null)
    {
        $video = $this;

        // Retrieve the formats property into a local variable
        $formats = $video->formats;

        // Iterate and modify the local variable
        foreach ($formats as $key => $f) {
            if ($f['format_id'] == $format_id) {
                if ($video_format != null) {
                    $formats[$key]['video_format'] = $video_format;
                }
                if ($video_size != null) {
                    $formats[$key]['video_size'] = $video_size;
                }
                if ($file_path != null) {
                    if ($file_path == -1) {
                        $formats[$key]['file_path'] = null;
                    } else {
                        $formats[$key]['file_path'] = $file_path;
                    }
                }
                if ($filename != null) {
                    $formats[$key]['filename'] = $filename;
                }
                break;
            }
        }

        // Set the modified array back to the property
        $video->formats = $formats;

        // Save the changes to the database
        $video->update();
    }



    public function videoPath()
    {
        $videoPath = $this->video_path;
        if ($videoPath != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $videoPath;
        } else {
            return null;
        }
    }

    public function comments()
    {
        return $this->hasMany(MongoVideoComment::class, 'video_id');
    }

    public function likes()
    {
        return $this->hasMany(MongoVideoLike::class, 'video_id');
    }


    public function hotComment()
    {
        return $this->comments()
            ->orderBy('like_count', 'desc')
            ->first();
    }

    public function imageAttr()
    {
        return $this->attributes['image'];
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
