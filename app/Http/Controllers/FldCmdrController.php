<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\{PollCount, Stub, TapZone, User};
use App\Eloquent\Messenger;
use Illuminate\Http\Request;

use App\Notifications\UserBroadcast;

class FldCmdrController extends Controller
{
    public function status(BotMan $bot)
    {
        // $status = [
        //     'verified' => true,
        //     'roles' => ['admin', 'subscriber'],
        //     'balance' => 0,
        // ];

        $user = Messenger::hook($bot)->getUser();
        // dd($user->status);
        $bot->reply($this->array_to_attributes($user->status));
    }

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
            $user->notify(new UserBroadcast($message));
        });



        $bot->reply(trans('broadcast.sent', ['count' => $users->count()]));
    }

    protected function array_to_attributes($array_attributes)
    {
        $attributes_str = NULL;
        foreach ( $array_attributes as $attribute => $value ) {
            if (is_array($value)){
                if (count($value) > 0)
                    $value = implode(',', $value);
                else
                    $value = 'nil';
            }

            if ($value === true)
                $value = 'true';

            if ($value === false)
                $value = 'false';

            $attributes_str .= $attribute . ': ' . $value . "\n" ;
        }
        $attributes_str .= 'Copyright: Applester 2018';

        return $attributes_str;
    }
}
