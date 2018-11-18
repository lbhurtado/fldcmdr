<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('test', function ($bot) {
    $bot->reply('It works!');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');

$botman->hears('/stop|\s', function(BotMan $bot) {
	$bot->reply('stopped...');
})->stopsConversation();

$dialogflow = Dialogflow::create('2a7576f8e70d445c89b6db456e0c3555')->listenForAction();
$botman->middleware->received($dialogflow);

$botman->fallback(function (BotMan $bot){
    return $bot->reply($bot->getMessage()->getExtras('apiReply'));
});