<?php

namespace App\Conversations;

use App\User;
use App\Helpers\Phone;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class Verify extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->setup()->introduction()->start();
    }

    protected function setup()
    {
    	$driver = $this->bot->getDriver()->getName();
    	$channel_id = $this->bot->getUser()->getId();

    	if ($user = User::where(compact('driver', 'channel_id'))->first()) {
    		if (! ($user->first_name || $user->last_name)) {
	    		$user->first_name = $this->bot->getUser()->getFirstName();
	    		$user->last_name = $this->bot->getUser()->getLastName();
		        $user->name = trim(ucfirst($user->first_name . ' ' . $user->last_name));
		        $user->save();    			
    		}
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
            if (! $mobile = $this->checkMobile($answer->getText()))
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

    private function getUser()
    {
    	return $this->user;
    }

    private function checkMobile($mobile)
    {
		return Phone::validate($mobile);
    }
}
