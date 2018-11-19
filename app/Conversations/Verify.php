<?php

namespace App\Conversations;

use App\User;
use App\Conversations\Survey;
use App\Eloquent\{Phone, Messenger};
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Eloquent\Conversation;

class Verify extends Conversation
{
    public function ready()
    {
        $this->setup()->introduction()->start();
    }

    protected function setup()
    {
        if ($user = $this->getMessenger()->impressUser()) {

    		$this->user = $user;
       };

    	return $this;
    }

    public function introduction()
    {
    	$this->bot->reply(trans('verify.introduction'));

    	return $this;
    }

    protected function start()
    {
    	if (empty($this->getUser()->mobile))
    		return $this->inputMobile();
    	else 
    		return $this->inputPIN($this->getUser());
    }

    protected function inputMobile()
    {
        $question = Question::create(trans('verify.input.mobile'))
            ->fallback(trans('verify.mobile.error'))
            ->callbackId('verify.input.mobile')
            ;

        return $this->ask($question, function (Answer $answer) {
            
            if (! $mobile = Phone::validate($answer->getText()))
                return $this->repeat(trans('verify.input.mobile'));

            tap($this->getUser(), function ($user) use ($mobile) {
        		$user->mobile = $mobile;
        		$user->save();
            })->refresh;
    		
            return $this->inputPIN();
        });
    }

    protected function inputPIN()
    {
    	$this->getUser()->challenge();

        $question = Question::create(trans('verify.input.pin'))
            ->fallback(trans('verify.pin.error'))
            ->callbackId('verify.input.pin')
            ;
                	
        return $this->ask($question, function (Answer $answer) {
            $otp = $answer->getText();

            if (! tap($this->getUser(), function ($user) use ($otp) {
            	$user->verify($otp); 
            	$user->refresh();
            })->isVerified())
            	return $this->inputPIN();

            return $this->finish();
        });   
    }

    protected function finish()
    {
        $this->bot->reply(trans('verify.success'));
    }

    protected function survey()
    {
        $this->bot->startConversation(new Survey());
    }

    private function getUser()
    {
    	return $this->user;
    }
}
