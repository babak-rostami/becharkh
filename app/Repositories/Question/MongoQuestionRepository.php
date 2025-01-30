<?php

namespace App\Repositories\Question;

use App\Models\MongoQuestion;
use App\RepositoryInterface\Question\QuestionRepositoryInterface;

class MongoQuestionRepository implements QuestionRepositoryInterface
{
    public function getlastQuestions()
    {
        return MongoQuestion::orderBy('created_at', 'desc')->where('status', 1)->get();
    }

    public function getQuestionsByItemId($categoryId, $itemId)
    {
        return MongoQuestion::orderBy('created_at', 'desc')->where('category_id', $categoryId)->where('items', $itemId)->where('status', 1)->get();
    }

    public function getQuestionsByCategoryId($categoryId)
    {
        return MongoQuestion::orderBy('created_at', 'desc')->where('category_id', $categoryId)->where('status', 1)->get();
    }
}
