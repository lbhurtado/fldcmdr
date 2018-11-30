<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\{PollCount, Stub, TapZone, User};
use App\Eloquent\Messenger;
use Illuminate\Http\Request;

use App\Notifications\UserBroadcast;

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

        $bot->reply(trans('signup.fence.center', $center));
    }

    public function broadcast(BotMan $bot, $message)
    {
        $users = User::all();
        $users->each(function($user) use ($bot, $message) {
            // $bot->say($message, $user->channel_id, $user->driver);
            $user->notify(new UserBroadcast($message));
        });



        $bot->reply(trans('broadcast.sent', ['count' => $users->count()]));
    }
}
