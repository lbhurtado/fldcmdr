<?php

namespace App\Eloquent;

use App\User;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\IncomingMessage as Message;

//helper class for botman middleware
class Missive
{
	protected $bot;

	protected $message;

	protected $user;

	public static function create(BotMan $bot, Message $message)
	{
		return new static($bot, $message);
	}

	public function __construct(BotMan $bot, Message $message)
	{
		$this->bot = $bot;
		$this->message = $message;
	}

	public function spawn()
	{
        $driver 	= $this->bot->getDriver()->getName();
        $channel_id = $this->message->getSender();
        $name 		= $this->generateName($driver, $channel_id);
        $password 	= $this->generatePassword($driver, $channel_id);
        $email 		= $this->generateEmail($driver, $channel_id);

        // if (! $this->user = User::firstOrCreate(compact('driver', 'channel_id'), compact('name', 'password', 'email')))
        // 	return false;
       
        $this->user = tap(User::firstOrCreate(compact('driver', 'channel_id'), compact('name', 'password', 'email')), function($user) {
        	if ($user->wasRecentlyCreated) {
        		$user->extra_attributes->wants_notifications = false;
        		$user->save();
        	}
        });


        return $this;
	}

	public function getUser()
	{
		return $this->user;
	}

	protected function generateName($driver, $channel_id)
	{
		return $driver . "." . $channel_id;
	}

	protected function generatePassword($driver, $channel_id)
	{
		return bcrypt(env('DEFAULT_PASSWORD', '1234'));
	}

	protected function generateEmail($driver, $channel_id)
	{
		$username = $driver . "." . $channel_id;
		return  $username . '@' . env('DEFAULT_DOMAIN_NAME', 'serbis.io');
	}
}
