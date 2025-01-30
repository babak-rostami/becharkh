<?php

namespace App\RepositoryInterface\CategoryComment;

interface CategoryCommentRepositoryInterface
{
    public function getParentComments($limit);
    public function getParentCommentsByCategoryId($categoryId, $limit);
    public function getParentCommentsByItemId($categoryId, $itemId, $limit);
}
