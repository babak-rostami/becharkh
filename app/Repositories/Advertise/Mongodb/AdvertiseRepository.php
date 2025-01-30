<?php

namespace App\Repositories\Advertise\Mongodb;

use App\Models\MongoAdvertise;
use App\RepositoryInterface\Advertise\AdvertiseRepositoryInterface;

class AdvertiseRepository implements AdvertiseRepositoryInterface
{
    public function getlastAdvertiseByPaginate($take)
    {
        return MongoAdvertise::orderBy('created_at', 'desc')->where('status', 1)->paginate($take);
    }

    public function getlastAdvertise($take, $category = null)
    {
        if ($category) {
            $advertises =  MongoAdvertise::orderBy('created_at', 'desc')->where('category_id', $category->id)->where('status', 1)->take($take)->get();
            return $advertises;
        }
    }

    public function getAdvertiseByItemIdByPaginate($categoryId, $itemId, $take)
    {
        return MongoAdvertise::orderBy('created_at', 'desc')->where('category_id', $categoryId)->where('items', $itemId)->where('status', 1)->paginate($take);
    }

    public function getAdvertiseByCategoryIdByPaginate($categoryId, $take)
    {
        return MongoAdvertise::orderBy('created_at', 'desc')->where('category_id', $categoryId)->where('status', 1)->paginate($take);
    }
}
