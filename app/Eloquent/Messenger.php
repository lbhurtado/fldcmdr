<?php

namespace App\Eloquent;

use App\User;
use BotMan\BotMan\BotMan;

class Messenger
{
	private $bot;

    public static function hook(BotMan $bot)
    {
        return new static($bot);
    }

    public function __construct(BotMan $bot)
    {
        $this->bot = $bot;
    }

    public function impressUser()
    {
        return tap($this->getUser(), function ($user) {
            if (! ($user->first_name || $user->last_name)) {
                $user->first_name = trim(ucfirst($this->getBot()->getUser()->getFirstName()));
                $user->last_name = trim(ucfirst($this->getBot()->getUser()->getLastName()));
                $user->name = $user->first_name . ' ' . $user->last_name;
                $user->save();                    
            }
        });
    }

    protected function getBot()
    {
        return $this->bot;
    }

    protected function getDriver()
    {
        return $this->bot->getDriver()->getName();
    }

    protected function getChannelId()
    {
        return $this->bot->getUser()->getId();
    }

    public function getUser()
    {
        return User::where(['driver' => $this->getDriver(), 'channel_id' => $this->getChannelId()])->firstOrFail();
    }
}
