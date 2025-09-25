<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoItemTelNumber extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'item_tel_numbers';

    public function item()
    {
        return $this->belongsTo(MongoItem::class, 'item_id');
    }
}
