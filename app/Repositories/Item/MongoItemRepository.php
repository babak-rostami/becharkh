<?php

namespace App\Repositories\Item;

use App\Models\MongoItem;
use App\RepositoryInterface\Item\ItemRepositoryInterface;

class MongoItemRepository implements ItemRepositoryInterface
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
}
