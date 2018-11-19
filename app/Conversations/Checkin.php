<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class Checkin extends Conversation
{
    public function run()
    {
        $this->introduction()->inputLocation();
    }

    protected function setup()
    {
    	$driver = $this->bot->getDriver()->getName();
    	$channel_id = $this->bot->getUser()->getId();

    	if ($user = User::fromMessenger($driver, $channel_id)) {
    		if ($user->isVerified()) {
			
    		}
    		$this->user = $user;
       };

    	return $this;
    }

    protected function introduction()
    {
    	$name = $this->user->name;
    	$this->bot->reply(trans('checkin.introduction', compact('name')));

    	return $this;
    }

}




