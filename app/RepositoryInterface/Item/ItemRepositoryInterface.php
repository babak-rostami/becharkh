<?php

namespace App\RepositoryInterface\Item;

interface ItemRepositoryInterface
{
    public function getItemsByCategoryAndStatus($categoryId, $status);
    public function getItemChildren($itemId);
    public function getItemById($itemId);
    public function getItemsByCategoryId($categoryId, $select = []);
    public function getItemsWithAllChildrenByFeatureId($featureId, $select = []);
}
