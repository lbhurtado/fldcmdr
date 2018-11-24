<?php

use BotMan\BotMan\BotMan;
use App\Http\Middleware\Messenger;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Conversations\{Survey, Verify, Checkin, Invite};
use App\Http\Controllers\{BotManController, FldCmdrController};

$botman = resolve('botman');

$dialogflow = Dialogflow::create('2a7576f8e70d445c89b6db456e0c3555')->listenForAction();
$botman->middleware->received($dialogflow);

$messenger = new Messenger;
$botman->middleware->received($messenger);

$botman->hears('test', function ($bot) {
    $bot->reply('It works!');
});

$botman->hears('/invite', function (BotMan $bot) {
    $bot->startConversation(new Invite());
})->stopsConversation();

$botman->hears('/checkin', function (BotMan $bot) {
    $bot->startConversation(new Checkin());
})->stopsConversation();

$botman->hears('/survey', function (BotMan $bot) {
    $bot->startConversation(new Survey());
})->stopsConversation();

$botman->hears('/poll', FldCmdrController::class.'@poll');

$botman->hears('/verify', function (BotMan $bot) {
    $bot->startConversation(new Verify());
})->stopsConversation();

$botman->hears('/start|GET_STARTED', function (BotMan $bot) {
    $bot->startConversation(new Verify());
})->stopsConversation();

$botman->hears('/stop|\s', function(BotMan $bot) {
	$bot->reply('stopped...');
})->stopsConversation();

$botman->hears('/bored', BotManController::class.'@startConversation');

$botman->fallback(function (BotMan $bot){
    return $bot->reply($bot->getMessage()->getExtras('apiReply'));
});

