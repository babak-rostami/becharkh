<?php

namespace App\Repositories\Category;

use App\Models\MongoCategory;
use App\RepositoryInterface\Category\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class MongoCategoryRepository implements CategoryRepositoryInterface
{
    public function getCategoryChildren($categoryId)
    {
        return MongoCategory::where('parent_id', $categoryId)->get();
    }

    public function getCategoryByIds($categoryIds)
    {
        return MongoCategory::find($categoryIds);
    }

    public function getCategoryBySlug($categorySlug)
    {
        return MongoCategory::where('slug', $categorySlug)->first();
    }

    public function getAllActiveCategoriesFromCache()
    {
        return Cache::rememberForever('categories', function () {
            return MongoCategory::where('status', 1)->get();
        });
    }
}
