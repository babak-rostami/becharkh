<?php

namespace App\RepositoryInterface\Question;

interface QuestionRepositoryInterface
{
    public function getlastQuestionsQuery();
    public function getQuestionsByCategoryIdQuery($categoryId);
}
