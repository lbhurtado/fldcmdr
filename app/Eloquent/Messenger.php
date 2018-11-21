<?php

namespace App\Eloquent;

use App\User;
use App\Invitation;
use BotMan\BotMan\BotMan;

class Messenger
{
	protected $bot;

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
            if (! ($user->extra_attributes->first_name || $user->extra_attributes->last_name)) {
                
                $first_name = trim(ucfirst($this->getBot()->getUser()->getFirstName()));
                $last_name = trim(ucfirst($this->getBot()->getUser()->getLastName()));

                $user->extra_attributes->first_name = $first_name;
                $user->extra_attributes->last_name = $last_name;
                $user->name = $first_name . ' ' . $last_name;
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
