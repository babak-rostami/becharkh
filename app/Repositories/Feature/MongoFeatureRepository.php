<?php

namespace App\Repositories\Feature;

use App\Models\MongoFeature;
use App\RepositoryInterface\Feature\FeatureRepositoryInterface;

class MongoFeatureRepository implements FeatureRepositoryInterface
{
    public function getFeaturesByCategory($categoryId)
    {
        return MongoFeature::where('category_id', $categoryId)->get();
    }

    public function getFeaturesByCategoryIdForForum($categoryId)
    {
        return MongoFeature::where('category_id', $categoryId)->where('is_in_filter_rtable', 1)->get();
    }

    public function getFeatureById($featureId)
    {
        return MongoFeature::find($featureId);
    }
}
