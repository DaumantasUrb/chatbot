<?php
namespace Rusted\QuizBotHardcore\test\QuizBot;

use Facebook\Facebook;
use PHPUnit\Framework\TestCase;
use Rusted\QuizBotHardcore\QuizBot\FacebookQuizBot;
use Rusted\QuizBotHardcore\QuizBot\Message;
use Rusted\QuizBotHardcore\QuizProvider\Quiz;
use Rusted\QuizBotHardcore\QuizProvider\QuizProviderInterface;

class FacebookQuizBotTest extends TestCase
{

    public function respondToMessageProvider()
    {
        return [
            [
                'INCORRECT',
                '',
                '1234',
                [
                    [
                        'messaging_type' => 'RESPONSE',
                        'recipient' => [
                            'id' => '1234',
                        ],
                        'message' => [
                            'text' => 'Incorrect!',
                        ]
                    ],
                    [
                        'messaging_type' => 'RESPONSE',
                        'recipient' => [
                            'id' => '1234',
                        ],
                        'message' => [
                            'text' => 'Your score is '. 1 . '. Would you like a question?',
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
                    ]
                ]
            ],
            [
                'CORRECT',
                '',
                '1234',
                [
                    [
                        'messaging_type' => 'RESPONSE',
                        'recipient' => [
                            'id' => '1234',
                        ],
                        'message' => [
                            'text' => 'Correct!',
                        ]
                    ],
                    [
                        'messaging_type' => 'RESPONSE',
                        'recipient' => [
                            'id' => '1234',
                        ],
                        'message' => [
                            'text' => 'Your score is '. 1 . '. Would you like a question?',
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
                    ]
                ]
            ],
            [
                'PLAY-YES',
                '',
                '1234',
                [
                    [
                        'messaging_type' => 'RESPONSE',
                        'recipient' => [
                            'id' => '1234',
                        ],
                        'message' => [
                            'text' => 'question',
                            "quick_replies" => [
                                [
                                    'content_type' => 'text',
                                    'title' => 'true',
                                    'payload' => 'CORRECT'
                                ],
                                [
                                    'content_type' => 'text',
                                    'title' => 'false',
                                    'payload' => 'INCORRECT'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'PLAY-NO',
                '',
                '1234',
                [
                    [
                        'messaging_type' => 'RESPONSE',
                        'recipient' => [
                            'id' => '1234',
                        ],
                        'message' => [
                            'text' => 'Bye',
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider respondToMessageProvider
     */
    public function testRespondToMessage($payload, $messageText, $userId, $messages)
    {
        $message = new Message();
        $message->setPayload($payload)
            ->setMessage($messageText)
            ->setUserId($userId)
        ;

        $fb = $this->getMockBuilder(Facebook::class)->disableOriginalConstructor()->getMock();
        $quizProvider = $this->getMockBuilder(QuizProviderInterface::class)->disableOriginalConstructor()->getMock();

        $quiz = new Quiz();
        $quiz->setQuestion('question');
        $quiz->setCorrectAnswer('true');
        $quiz->setIncorrectAnswer('false');
        $quizProvider
            ->method('getNewQuiz')
            ->willReturn($quiz)
        ;

        $bot = new FacebookQuizBot($fb, $quizProvider);

        foreach ($messages as $key => $expectedMessage) {
            $fb
                ->expects($this->at($key))
                ->method('post')
                ->with(
                        '/me/messages',
                        $expectedMessage,
                        FacebookQuizBot::ACCESS_TOKEN
                );

        }

        $this->assertTrue(true);
        $bot->respondToMessage($message);
    }

}