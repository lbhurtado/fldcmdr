<?php

namespace App\Eloquent;

use App\Eloquent\Messenger;
use BotMan\BotMan\Messages\Conversations\Conversation as BaseConversation;

abstract class Conversation extends BaseConversation
{
	private $messenger;

    abstract public function ready();

    public function run()
    {
    	$this->admin()->ready();
    }

    protected function admin()
    {
    	$this->messenger = Messenger::create($this->bot);

    	return $this;
    }

    protected function getMessenger()
    {
    	return $this->messenger;
    }
}
