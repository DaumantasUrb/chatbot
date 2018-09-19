<?php
namespace Rusted\QuizBotHardcore\test\QuizProvider;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\StreamInterface;
use PHPUnit\Framework\TestCase;
use Rusted\QuizBotHardcore\QuizProvider\Quiz;
use Rusted\QuizBotHardcore\QuizProvider\QuizProviderInterface;
use Rusted\QuizBotHardcore\QuizProvider\TDBQuizProvider;
use GuzzleHttp\Client;

class TDBQuizProviderTest extends TestCase
{
    public function testgetNewQuiz()
    {
        $guzzleClientMock = $this->getMockBuilder(Client::class)->getMock();

        $bodyMock = $this->getMockBuilder(StreamInterface::class)->getMock();
        $bodyMock
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('{"response_code":0,"results":[{"category":"Entertainment: Cartoon & Animations","type":"boolean","difficulty":"easy","question":"Waylon Smithers from &quot;The Simpsons&quot; was originally black when he first appeared in the series.","correct_answer":"True","incorrect_answers":["False"]}]}')
        ;

        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($bodyMock)
        ;

        $guzzleClientMock->expects($this->once())
            ->method('get')
            ->willReturn($responseMock)
        ;

        /** @var QuizProviderInterface $tdbQuizProviderMock */
        $tdbQuizProvider = new TDBQuizProvider($guzzleClientMock);

        $quiz = $tdbQuizProvider->getNewQuiz();
        $this->assertInstanceOf(Quiz::class, $quiz);
        $this->assertEquals($quiz->getQuestion(), 'Waylon Smithers from &quot;The Simpsons&quot; was originally black when he first appeared in the series.');
        $this->assertEquals($quiz->getCorrectAnswer(), 'True');
        $this->assertEquals($quiz->getIncorrectAnswer(), 'False');
    }

}