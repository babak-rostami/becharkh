<?php

namespace App\Repositories\CategoryComment\Mongodb;

use App\Models\MongoCategoryComment;
use App\RepositoryInterface\CategoryComment\CategoryCommentRepositoryInterface;

class CategoryCommentRepository implements CategoryCommentRepositoryInterface
{
    public function getParentComments($limit)
    {
        return MongoCategoryComment::orderBy('created_at', 'desc')
            ->whereNull('parent_id')
            ->take($limit)
            ->with('user')
            ->get();
    }

    public function getParentCommentsByItemId($categoryId, $itemId, $limit)
    {
        return MongoCategoryComment::orderBy('created_at', 'desc')
            ->whereNull('parent_id')
            ->where('category_id', $categoryId)
            ->where('items', $itemId)
            ->take($limit)
            ->with('user')
            ->get();
    }

    public function getParentCommentsByCategoryId($categoryId, $limit)
    {
        return MongoCategoryComment::orderBy('created_at', 'desc')
            ->whereNull('parent_id')
            ->where('category_id', $categoryId)
            ->take($limit)
            ->with('user')
            ->get();
    }
}
