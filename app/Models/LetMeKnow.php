<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class LetMeKnow extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'let_me_knows';

    public function category()
    {
        return $this->belongsTo(MongoCategory::class, 'category_id');
    }

    public function item()
    {
        return $this->belongsTo(MongoItem::class, 'item_id');
    }
}
