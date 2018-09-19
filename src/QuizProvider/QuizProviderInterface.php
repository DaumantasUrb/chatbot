<?php
namespace Rusted\QuizBotHardcore\QuizProvider;

interface QuizProviderInterface
{
    public function getNewQuiz() : Quiz;
}
