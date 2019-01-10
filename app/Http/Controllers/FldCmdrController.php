<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\Eloquent\Messenger;
use Illuminate\Http\Request;
use App\Exports\RolesExport;
use App\Notifications\UserBroadcast;
use Maatwebsite\Excel\Facades\Excel;
use App\{PollCount, Stub, TapZone, User};


class FldCmdrController extends Controller
{
    public function status(BotMan $bot)
    {
        $user = Messenger::hook($bot)->getUser();

        $bot->reply($this->array_to_attributes($user->status));
    }

    public function info(BotMan $bot)
    {
        $user = Messenger::hook($bot)->getUser();

        $bot->reply($this->array_to_attributes($user->info));
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
        $origin = Messenger::hook($bot)->getUser();

        $users = User::all();
        $users->each(function($user) use ($origin, $bot, $message) {
            $user->notify(new UserBroadcast($origin, $message));
        });

        $bot->reply(trans('broadcast.sent', ['count' => $users->count()]));
    }

    public function reports(BotMan $bot)
    {
        $bot->reply(trans('chatbot.list.reports'));
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
            if (empty($value))
                $value = 'nil';
            
            if ($value === true)
                $value = 'true';

            if ($value === false)
                $value = 'false';

            $attributes_str .= $attribute . ': ' . $value . "\n" ;
        }
        $attributes_str .= "\nCopyright Applester 2018";

        return $attributes_str;
    }
}
