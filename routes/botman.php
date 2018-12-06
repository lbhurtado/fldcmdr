<?php

use BotMan\BotMan\BotMan;
use App\Http\Middleware\{HookMessenger, AddTyping};
use BotMan\BotMan\Middleware\Dialogflow;
use App\Conversations\{Survey, Verify, Checkin, Invite, Signup, Onboarding};
use App\Http\Controllers\{BotManController, FldCmdrController};

$botman = resolve('botman');

$dialogflow = Dialogflow::create('2a7576f8e70d445c89b6db456e0c3555')->listenForAction();
$botman->middleware->received($dialogflow);

$botman->middleware->sending(new AddTyping);
$botman->middleware->received(new HookMessenger);

$botman->hears('test', function ($bot) {
    $bot->reply('It works!');
});

$botman->hears('/status', FldCmdrController::class.'@status');

$botman->hears('/info', FldCmdrController::class.'@info');

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
    $bot->startConversation(new Onboarding());
})->stopsConversation();

$botman->hears('/woo', FldCmdrController::class.'@woo');

$botman->hears('/fence', FldCmdrController::class.'@fence');

$botman->hears('/signup', function (BotMan $bot) {
    $bot->startConversation(new Signup());
})->stopsConversation();

$botman->hears('/stop|\s', function(BotMan $bot) {
	$bot->reply('stopped...');
})->stopsConversation();

$botman->hears('/bored', BotManController::class.'@startConversation');

$botman->hears('@all {message}', FldCmdrController::class.'@broadcast');

$botman->hears('/reports', FldCmdrController::class.'@reports');

$botman->fallback(function (BotMan $bot){
    if ($bot->getMessage()->getExtras('is_new_user'))
        return $bot->startConversation(new Onboarding);

    return $bot->reply($bot->getMessage()->getExtras('apiReply'));
});

