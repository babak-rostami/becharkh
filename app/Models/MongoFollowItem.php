<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoFollowItem extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'follow_items';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function item()
    {
        return $this->belongsTo(MongoItem::class, 'item_id');
    }
}
