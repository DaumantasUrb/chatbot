<?php
namespace Rusted\QuizBotHardcore\QuizBot;

use Facebook\Facebook;
use Rusted\QuizBotHardcore\QuizProvider\QuizProviderInterface;

class FacebookQuizBot implements QuizBotInterface
{
    const ACCESS_TOKEN = 'YOUR_FACEBOOK_TOKEN_HERE';
    protected $facebook;
    protected $quizProvider;

    public function __construct(Facebook $facebook, QuizProviderInterface $quizProvider)
    {
        $this->facebook = $facebook;
        $this->quizProvider = $quizProvider;
    }

    public function getMessage() : ?Message
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input === null) {
            return null;
        }

        $text = $input['entry'][0]['messaging'][0]['message']['text'];
        $quickreplyPayload = $input['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];

        $message = new Message();
        $message
            ->setMessage($text)
            ->setPayload($quickreplyPayload)
            ->setUserId($sender)
        ;

        return $message;
    }

    /**
     * @param Message $message
     */
    public function respondToMessage(Message $message)
    {
        switch ($message->getPayload()) {
            case 'INCORRECT':
                $this->respondIncorrectAnswer($message->getUserId());
                $this->showPlayDialog($message->getUserId());
                return;
            case 'CORRECT':
                $this->respondCorrectAnswer($message->getUserId());
                $this->showPlayDialog($message->getUserId());
                return;
            case 'PLAY-YES':
                $this->showQuestionDialog($message->getUserId());
                return;
            case 'PLAY-NO':
                $this->respondPlayNo($message->getUserId());
                return;
        }

        $this->showPlayDialog($message->getUserId());

        return;
    }

    public function getScore($senderId)
    {
        return (int)file_get_contents('score.txt');
    }

    public function increaseScore($senderId)
    {
        $score = $this->getScore($senderId);
        $score++;
        file_put_contents('score.txt', $score);

    }

    private function respondIncorrectAnswer($senderId)
    {
        $data = [
            'messaging_type' => 'RESPONSE',
            'recipient' => [
                'id' => $senderId,
            ],
            'message' => [
                'text' => 'Incorrect!',
            ]
        ];

        $response = $this->facebook->post('/me/messages', $data, self::ACCESS_TOKEN);

    }

    private function respondCorrectAnswer($senderId)
    {
        $this->increaseScore($senderId);
        $data = [
            'messaging_type' => 'RESPONSE',
            'recipient' => [
                'id' => $senderId,
            ],
            'message' => [
                'text' => 'Correct!',
            ]
        ];

        $response = $this->facebook->post('/me/messages', $data, self::ACCESS_TOKEN);
    }

    private function showQuestionDialog($senderId)
    {
        $quiz = $this->quizProvider->getNewQuiz();
        $data = [
            'messaging_type' => 'RESPONSE',
            'recipient' => [
                'id' => $senderId,
            ],
            'message' => [
                'text' => html_entity_decode($quiz->getQuestion()),
                "quick_replies" => [
                    [
                        'content_type' => 'text',
                        'title' => $quiz->getCorrectAnswer(),
                        'payload' => 'CORRECT'
                    ],
                    [
                        'content_type' => 'text',
                        'title' => $quiz->getIncorrectAnswer(),
                        'payload' => 'INCORRECT'
                    ]
                ]
            ]
        ];

        $response = $this->facebook->post('/me/messages', $data, self::ACCESS_TOKEN);
    }

    private function respondPlayNo($senderId)
    {
        $data = [
            'messaging_type' => 'RESPONSE',
            'recipient' => [
                'id' => $senderId,
            ],
            'message' => [
                'text' => 'Bye',
            ]
        ];

        $response = $this->facebook->post('/me/messages', $data, self::ACCESS_TOKEN);
    }

    private function showPlayDialog($userId)
    {
        $data = [
            'messaging_type' => 'RESPONSE',
            'recipient' => [
                'id' => $userId,
            ],
            'message' => [
                'text' => 'Your score is '. $this->getScore($userId) . '. Would you like a question?',
                "quick_replies" => [
                    [
                        'content_type' => 'text',
                        'title' => 'YES',
                        'payload' => 'PLAY-YES'
                    ],
                    [
                        'content_type' => 'text',
                        'title' => 'NO',
                        'payload' => 'PLAY-NO'
                    ]
                ]
            ]
        ];

        $response = $this->facebook->post('/me/messages', $data, self::ACCESS_TOKEN);
    }
}
