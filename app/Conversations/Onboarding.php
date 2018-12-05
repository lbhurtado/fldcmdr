<?php

namespace App\Conversations;

use App\Eloquent\Conversation;
use App\Conversations\{Verify, Signup};
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class Onboarding extends Conversation
{
    const SIGNUP = true;

    const VERIFY = false;

    protected $user; 

    public function ready()
    {
        $this->setup()->introduction()->start();
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
    
    protected function setup()
    {
        $this->user = $this->getMessenger()->getUser();

        return $this;
    }

    protected function start()
    {
    	$this->optin();
    }

    protected function optin()
    {
        $question = Question::create(trans('onboarding.question.optin'))
            ->fallback(trans('onboarding.question.error'))
            ->callbackId('onboarding.question.optin')
            ->addButton(Button::create(trans('onboarding.answer.optin.affirmative'))->value('yes'))
            ->addButton(Button::create(trans('onboarding.answer.optin.negative'))->value('no'))
            ;

        return $this->ask($question, function (Answer $answer) {
            if (! $answer->isInteractiveMessageReply())
                return $this->repeat();

            return ($answer->getValue() == 'yes')
                    ? $this->withStub()
                    : $this->regrets();
        });
    }

    protected function withStub()
    {
        $question = Question::create(trans('onboarding.question.stub'))
            ->fallback(trans('onboarding.question.error'))
            ->callbackId('onboarding.question.stub')
            ->addButton(Button::create(trans('onboarding.answer.stub.affirmative'))->value('yes'))
            ->addButton(Button::create(trans('onboarding.answer.stub.negative'))->value('no'))
            ;

        return $this->ask($question, function (Answer $answer) {
            if (! $answer->isInteractiveMessageReply())
                return $this->repeat();

            return ($answer->getValue() == 'yes')
                    ? $this->process(self::SIGNUP)
                    : $this->process(self::VERIFY);
        });    
    }

    protected function process($signup = false)
    {
    	$this->say(trans('onboarding.processing'));
    	$this->say(trans('onboarding.processed'));
        if ($signup == true)
            $this->bot->startConversation(new Signup());
        else    
            $this->bot->startConversation(new Verify());
    }

    protected function regrets()
    {
        $this->say(trans('onboarding.regrets'));
        $this->user->delete();
        $this->say(trans('onboarding.expunge'));
    }
}
