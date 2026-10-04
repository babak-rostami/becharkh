<?php

namespace App\Repositories\Question\Mongodb;

use App\Models\MongoQuestion;
use App\RepositoryInterface\Question\QuestionRepositoryInterface;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function getlastQuestionsQuery()
    {
        return MongoQuestion::orderBy('created_at', 'desc')->where('status', 1);
    }

    public function getQuestionsByCategoryIdQuery($categoryId)
    {
        return MongoQuestion::orderBy('created_at', 'desc')->where('category_id', $categoryId)->where('status', 1);
    }
}
