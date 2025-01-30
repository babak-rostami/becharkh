<?php

namespace App\RepositoryInterface\Question;

interface QuestionRepositoryInterface
{
    public function getlastQuestions();
    public function getQuestionsByItemId($categoryId, $itemId);
    public function getQuestionsByCategoryId($categoryId);
}
