<?php

namespace App\Conversations;

use App\Eloquent\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class Onboarding extends Conversation
{
    public function ready()
    {
        $this->introduction()->start();
    }

    public function introduction()
    {
    	$this->say(trans('onboarding.introduction.1'));
    	sleep(1);
    	$this->say(trans('onboarding.introduction.2'));
    	sleep(1);
    	$this->say(trans('onboarding.introduction.3'));
    	sleep(1);
    	$this->say(trans('onboarding.introduction.4'));

    	return $this;
    }

    protected function start()
    {
    	$this->optin();
    }

    protected function optin()
    {
        $question = Question::create(trans('onboarding.input.optin'))
            ->fallback(trans('onboarding.input.error'))
            ->callbackId('onboarding.input.optin')
            ->addButton(Button::create(trans('onboarding.optin.affirmative'))->value(true))
            ->addButton(Button::create(trans('onboarding.optin.negative'))->value(false))
            ;

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue())
                	return $this->process();
            }
            else 
                return $this->repeat();
        });
    }

    protected function process()
    {
    	$this->say(trans('onboarding.processing'));
    	$this->say(trans('onboarding.processed'));
        $this->bot->startConversation(new Verify());
    }
}
