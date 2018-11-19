<?php

namespace App\Eloquent;

use App\User;
use BotMan\BotMan\BotMan;

class Messenger
{
	protected $bot;

    public static function create(BotMan $bot)
    {
        return new static($bot);
    }

    public static function spawn(BotMan $bot)
    {
        return tap(static::create($bot), function ($messenger) {
            $driver = $messenger->getBot()->getDriver()->getName();
            // $channel_id = $messenger->getBot()->getUser()->getId();
            // $name = $driver . ":" . $channel_id;
            // $password = bcrypt('1234');
            // $email = $name . '@serbis.io';

        });
    }

    public function __construct(BotMan $bot)
    {
        $this->bot = $bot;
    }

    public function impressUser()
    {
        return tap($this->getUser(), function ($user) {
            if (! ($user->first_name || $user->last_name)) {
                $user->first_name = trim(ucfirst($this->bot->getUser()->getFirstName()));
                $user->last_name = trim(ucfirst($this->bot->getUser()->getLastName()));
                $user->name = $user->first_name . ' ' . $user->last_name;
                $user->save();                    
            }
        });
    }

    public function conjureUser()
    {
        $driver = $this->bot->getDriver()->getName();
        $channel_id = $this->bot->getUser()->getId();
        $name = $driver . ":" . $channel_id;
        $password = bcrypt('1234');
        $email = $name . '@serbis.io';

        // User::firstOrCreate(compact('driver', 'channel_id'), compact('name', 'password', 'email'));

        return $this;
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
