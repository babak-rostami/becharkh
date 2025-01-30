<?php

namespace App\Repositories\Item\Mongodb;

use App\Models\MongoFeature;
use App\Models\MongoItem;
use App\RepositoryInterface\Item\ItemRepositoryInterface;

class ItemRepository implements ItemRepositoryInterface
{
    public function getItemsByCategoryAndStatus($categoryId, $status)
    {
        return MongoItem::where('category_id', $categoryId)->where('status', $status)->get();
    }

    public function getItemChildren($itemId)
    {
        return MongoItem::where('parent_id', $itemId)->get();
    }

    public function getItemById($itemId)
    {
        return MongoItem::find($itemId);
    }

    public function getItemsByCategoryId($categoryId, $select = [])
    {
        $query = MongoItem::where('category_id', $categoryId);

        if (!empty($select)) {
            $query->select($select);
        }

        return $query->get();
    }

    public function getItemsWithAllChildrenByFeatureId($featureId, $select = [])
    {
        $features = MongoFeature::where('parent_id', $featureId)->get();
        $allFeatureIds = [];
        $this->getAllChildrenFeatureIds($features, $allFeatureIds);
        $allFeatureIds[] = $featureId;
        $query = MongoItem::whereIn('feature_id', $allFeatureIds);
        if (!empty($select)) {
            $query->select($select);
        }
        return $query->get();
    }

    private function getAllChildrenFeatureIds($features, &$allFeatureIds)
    {
        foreach ($features as $feature) {
            $allFeatureIds[] = $feature->id;
            $children = MongoFeature::where('parent_id', $feature->id)->get();
            if ($children->isNotEmpty()) {
                $this->getAllChildrenFeatureIds($children, $allFeatureIds);
            }
        }
    }
}
