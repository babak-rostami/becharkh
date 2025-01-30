<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoAdvertiseFeatureValue extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'advertise_feature_value';

    protected $fillable = [
        'advertise_id',
        'feature_id',
        'value',
        'created_at',
        'updated_at',
    ];

    public function feature()
    {
        return $this->hasOne(MongoFeature::class, '_id', 'feature_id');
    }

}
