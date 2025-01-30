<?php

namespace App\RepositoryInterface\Feature;

interface FeatureRepositoryInterface
{
    public function getFeaturesByCategory($categoryId);
    public function getFeatureById($featureId);
    public function getFeaturesByCategoryIdForForum($categoryId);
}
