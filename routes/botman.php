<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('test', function ($bot) {
    $bot->reply('It works!');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
