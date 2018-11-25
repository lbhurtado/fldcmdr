<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\{PollCount, Stub};
use App\Eloquent\Messenger;
use Illuminate\Http\Request;

class FldCmdrController extends Controller
{
    public function poll(BotMan $bot)
    {
        $items = PollCount::result();

        $text = '';
        $items->each(function($item, $key) use (&$text) {
        	$text .= $key . "➡" . $item .  "\n";
        });

        $bot->reply($text);
    }

    public function woo(BotMan $bot)
    {
    	$messenger = Messenger::hook($bot);
    	$user = $messenger->getUser();

    	$stub = Stub::generate($user);

    	$bot->reply(trans('signup.woo.stub', compact('stub')));
    }
}
