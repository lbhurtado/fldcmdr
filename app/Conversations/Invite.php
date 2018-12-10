<?php

namespace App\Conversations;

use App\{User, Invitee};
use App\Eloquent\Conversation;
use Spatie\Permission\Models\Role;
use App\Eloquent\{Phone, Messenger};
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;


class Invite extends Conversation
{
	protected $user;

	protected $roles;

    public function ready()
    {
        $this->setup()->introduction()->start();
    }

    protected function setup()
    {
    	$this->user = $this->getMessenger()->getUser();

    	$this->roles = Role::where('name', '!=', 'admin')->pluck('name');

    	return $this;
    }

    public function introduction()
    {
    	$this->bot->reply(trans('invite.introduction'));

    	return $this;
    }

    protected function start()
    {
    	$this->inputMobile();
    }

    protected function inputMobile()
    {
        $question = Question::create(trans('invite.input.mobile'))
            ->fallback(trans('invite.mobile.error'))
            ->callbackId('invite.input.mobile')
            ;

        return $this->ask($question, function (Answer $answer) {
            
            if (! $this->mobile = Phone::validate($answer->getText()))
                return $this->repeat(trans('invite.input.mobile'));
    		
    		return $this->inputrole();
        });
    }

    protected function inputRole()
    {
        $question = Question::create(trans('invite.input.role'))
        ->fallback(trans('invite.role.error'))
        ->callbackId('invite_role')
        ;

        $this->roles->each(function($role) use ($question) {
            $question->addButton(Button::create(ucfirst($role))->value($role));
        });

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->role = $answer->getValue();

                return $this->process();
            }
            else 
                return $this->repeat();
        });
    }

    protected function process()
    {
        $this->bot->reply(trans('invite.processing'));

    	$invitee = $this->getUser()
    			->invitees()
				->updateOrCreate([
					'mobile' => $this->mobile
				]
    //             ,[
				// 	'role' => $this->role,
				// 	'message' => trans('invite.message'),
				// ]
            );
        $invitee->role = $this->role;
        $invitee->message = trans('invite.message');
        $invitee->save();

		$invitee->send();

        $this->bot->reply(trans('invite.processed'));
    }

    protected function getUser()
    {
    	return $this->user;
    }
}
