<?php

namespace App\Repositories\Feature\Mongodb;

use App\Models\MongoCategory;
use App\Models\MongoFeature;
use App\RepositoryInterface\Feature\FeatureRepositoryInterface;

class FeatureRepository implements FeatureRepositoryInterface
{
    public function getFeaturesByCategory($categoryId)
    {
        return MongoCategory::find($categoryId)->features();
    }

    public function getFeaturesByCategoryIdForForum($categoryId)
    {
        return $this->getFeaturesByCategory($categoryId)->where('is_in_filter_rtable', 1);
    }

    public function getFeatureById($featureId)
    {
        return MongoFeature::find($featureId);
    }
}
