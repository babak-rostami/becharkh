<?php

namespace App\Repositories\CategoryComment;

use App\Models\MongoCategoryComment;
use App\RepositoryInterface\CategoryComment\CategoryCommentRepositoryInterface;

class MongoCategoryCommentRepository implements CategoryCommentRepositoryInterface
{
    public function getParentComments($limit)
    {
        return MongoCategoryComment::whereNull('parent_id')
            ->orderBy('created_at', 'desc')
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
