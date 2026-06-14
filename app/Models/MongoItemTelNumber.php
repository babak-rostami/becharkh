<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class MongoItemTelNumber extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'item_tel_numbers';

    public function item()
    {
        return $this->belongsTo(MongoItem::class, 'item_id');
    }
}
