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
        $driver 	= $this->getDriver();
        $channel_id = $this->message->getSender();       
        $this->user = User::firstOrCreate(compact('driver', 'channel_id'));

        return $this;
	}

	protected function getDriver()
	{
		$driver = $this->bot->getDriver()->getName();
		
		switch ($driver) {
			case 'TelegramLocation':
				$driver = 'Telegram';
				break;
			
			default:
				# code...
				break;
		}

		return $driver;
	}

	public function getUser()
	{
		return $this->user;
	}
}
