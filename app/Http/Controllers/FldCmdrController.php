<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\{PollCount, Stub, TapZone};
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
    	$user = Messenger::hook($bot)->getUser();

    	$stub = Stub::generate($user);

    	$bot->reply(trans('signup.woo.stub', compact('stub')));
    }

    public function fence(BotMan $bot)
    {
        $user = Messenger::hook($bot)->getUser();
        $center = TapZone::generate($user);
        $bot->reply('checkin hashtag: ' . $center['logitude']);

        // $bot->reply(trans('signup.fence.center', compact('center'))
    }
}
