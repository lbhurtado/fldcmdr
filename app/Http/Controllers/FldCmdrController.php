<?php

namespace App\Http\Controllers;

use App\PollCount;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;

class FldCmdrController extends Controller
{
    public function poll(BotMan $bot)
    {
        $items = PollCount::result();

        $text = '';
        $items->each(function($item, $key) use (&$text) {
        	$text .= $key . "=" . $item .  "\n";
        });

        $bot->reply($text);
    }
}
