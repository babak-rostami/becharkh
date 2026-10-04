<?php

namespace App\RepositoryInterface\Advertise;

interface AdvertiseRepositoryInterface
{
    public function getlastAdvertiseByPaginate($take);
    public function getlastAdvertise($take, $category = null);
    public function getAdvertiseByItemIdByPaginate($categoryId, $itemId, $take);
    public function getAdvertiseByCategoryIdByPaginate($categoryId, $take);
}
