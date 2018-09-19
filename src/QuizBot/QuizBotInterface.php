<?php
namespace Rusted\QuizBotHardcore\QuizBot;

interface QuizBotInterface
{
    public function getMessage(): ?Message;

    public function respondToMessage(Message $message);
}
