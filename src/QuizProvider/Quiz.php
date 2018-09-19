<?php

namespace Rusted\QuizBotHardcore\QuizProvider;

class Quiz
{
    private $question;

    private $correctAnswer;

    private $incorrectAnswer;

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     * @return Quiz
     */
    public function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCorrectAnswer()
    {
        return $this->correctAnswer;
    }

    /**
     * @param mixed $correctAnswer
     * @return Quiz
     */
    public function setCorrectAnswer($correctAnswer)
    {
        $this->correctAnswer = $correctAnswer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIncorrectAnswer()
    {
        return $this->incorrectAnswer;
    }

    /**
     * @param mixed $incorrectAnswer
     * @return Quiz
     */
    public function setIncorrectAnswer($incorrectAnswer)
    {
        $this->incorrectAnswer = $incorrectAnswer;
        return $this;
    }
}