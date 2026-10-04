<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoFollowItem extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'follow_items';

    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    public function item()
    {
        return $this->belongsTo(MongoItem::class, 'item_id');
    }
}
