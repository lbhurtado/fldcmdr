<?php

namespace App\Conversations;

use App\Stub;
use App\Conversations\Verify;
use App\Eloquent\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;

class Signup extends Conversation
{
    const NO_INTRO = false;

	protected $user;

	protected $stub;

    public function ready()
    {
        $this->setup()->introduction()->start();
    }

    protected function setup()
    {
    	$this->user = $this->getMessenger()->getUser();

    	return $this;
    }

    public function introduction()
    {
    	$this->bot->reply(trans('signup.introduction'));

    	return $this;
    }

    protected function start()
    {
    	$this->inputStub();
    }

    protected function inputStub()
    {
        $question = Question::create(trans('signup.input.stub'))
            ->fallback(trans('signup.stub.error'))
            ->callbackId('signup.input.stub')
            ;

        return $this->ask($question, function (Answer $answer) {
            
            if (! $this->stub = Stub::validate($answer->getText()))
                return $this->repeat(trans('signup.input.stub'));
    		
    		return $this->process();
        });
    }

    protected function process()
    {
    	// $this->bot->reply(trans('signup.processing'));
    	$upline = $this->stub->user;
        $upline->appendNode($this->user);
    	// $this->bot->reply(trans('signup.processed'));
    	$this->bot->startConversation(new Verify(self::NO_INTRO));
    }
}
