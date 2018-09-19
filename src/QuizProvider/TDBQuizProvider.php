<?php

namespace Rusted\QuizBotHardcore\QuizProvider;

use GuzzleHttp\Client;

class TDBQuizProvider implements QuizProviderInterface
{
    const QUIZ_API_URL = 'api.php?amount=1&type=boolean';

    protected $tdbClient;

    public function __construct(Client $tdbClient)
    {
        $this->tdbClient = $tdbClient;
    }

    public function getNewQuiz(): Quiz
    {
        $contents = $this->getNewQuizJSONFromApi();
        $lastQuestion =json_decode($contents, true);
        $question = $lastQuestion['results'][0]['question'];
        $answer = $lastQuestion['results'][0]['correct_answer'];
        $incorrectAnswer = $lastQuestion['results'][0]['incorrect_answers'][0];
        $quiz = new Quiz();
        $quiz
            ->setCorrectAnswer($answer)
            ->setIncorrectAnswer($incorrectAnswer)
            ->setQuestion($question)
        ;

        return $quiz;
    }

    private function getNewQuizJSONFromApi()
    {
        $response = $this->tdbClient->get(self::QUIZ_API_URL);
        $contents = ($response->getBody()->getContents());

        return $contents;
    }
}