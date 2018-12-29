<?php

namespace App;

use App\{User, Contact};
use App\Contracts\Sociable;

class Command
{
	private $user;

	private $sociable;

    public static function tag($mobile, $stochastic = null)
    {
    	//improve on this
    	$sociable = User::findByMobile($mobile) ?? Contact::findByMobile($mobile);

    	return (new static($sociable))->createTag($stochastic);
    }

    public function __construct(Sociable $sociable)
    {
    	$this->sociable = $sociable;
    }

    public function createTag($stochastic = null)
    {
    	$code = $this->generateCode($stochastic);
    	$sociable = $this->getSociable();

    	return tap(Tag::createWithTagger(compact('code'), $sociable), function ($tag) {
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

    protected function getSociable()
    {
    	return $this->sociable;
    }

    //improve on this
    protected function getContextGroup()
    {
    	return $this->getSociable()->groups()->latest()->first();
    }

    //improve on this
    protected function getContextRole()
    {
    	return $this->getSociable()->roles()->latest()->first();
    }

}
