<?php

namespace App\RepositoryInterface\Category;

interface CategoryRepositoryInterface
{
    public function getCategoryChildren($categoryId);
    public function getCategoryByIds($categoryIds);

    public function getAllActiveCategoriesFromCache();
    public function getCategoryBySlug($slug);
}
