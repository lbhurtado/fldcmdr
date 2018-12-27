<?php

namespace App;

use App\User;

class Command
{
	private $user;

    public static function tag($mobile, $stochastic = null)
    {
    	return (new static(User::findByMobile($mobile)))->createTag($stochastic);
    }

    public function __construct(User $user)
    {
    	$this->user = $user;
    }

    public function createTag($stochastic = null)
    {
    	$code = $this->generateCode($stochastic);
    	$user = $this->getUser();

    	return tap(Tag::createWithTagger(compact('code'), $user), function ($tag) {
    		optional($this->getContextGroup(), function ($group) use ($tag) {
    			$tag->setGroup($group);    			
    		});
    		optional($this->getContextRole(), function ($role)  use ($tag) {
    			$tag->setRole($role);	
    		});
    		
    	});
    }

    protected function generateCode($seed = null)
    {
    	return $seed ?? str_random(6);
    }

    protected function getUser()
    {
    	return $this->user;
    }

    protected function getContextGroup()
    {
    	return $this->getUser()->groups()->latest()->first();
    }

    protected function getContextRole()
    {
    	return $this->getUser()->roles()->latest()->first();
    }

}
