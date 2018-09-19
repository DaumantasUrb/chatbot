<?php

include (__DIR__ . '/vendor/autoload.php');

use GuzzleHttp\Client;
use \Rusted\QuizBotHardcore\QuizBot\FacebookQuizBot;

function logTxt ($msg)
{
    file_put_contents('out.txt', $msg . PHP_EOL, FILE_APPEND);
}

$access_token = 'ACCESS_TOKEN_HERE';
$verify_token = 'TOKEN';
$appId = 'APP_ID_HERE';
$appSecret = 'APP_SECRET_HERE';

$fb = new \Facebook\Facebook([
    'app_id' => $appId,
    'app_secret' => $appSecret,
]);

if (isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    if ($_REQUEST['hub_verify_token'] === $verify_token) {
        echo $challenge; die();
    }
}


$client = new GuzzleHttp\Client(['base_url' => 'https://opentdb.com']);
$quizProvider = new \Rusted\QuizBotHardcore\QuizProvider\TDBQuizProvider($client);
$quizBot = new FacebookQuizBot($fb, $quizProvider);

$message = $quizBot->getMessage();
$quizBot->respondToMessage($message);

