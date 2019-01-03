<?php

namespace App;

use App\{User, Contact, AirTime};
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


    public static function claim($mobile, $stochastic)
    {
        //improve on this
        $sociable = Contact::firstOrCreate(compact('mobile'));

        return (new static($sociable))->claimTag($stochastic);
    }

    public function __construct(Sociable $sociable)
    {
    	$this->sociable = $sociable;
    }

    protected function createTag($stochastic = null)
    {
    	$code = $this->generateCode($stochastic);
    	$sociable = $this->getSociable();

    	return tap(Tag::createWithTagger(compact('code'), $sociable), function ($tag) {
    		optional($this->getContextGroup(), function ($group) use ($tag) {
    			$tag->setGroup($group);    			
    		});
    		// optional($this->getContextRole(), function ($role)  use ($tag) {
    		// 	$tag->setRole($role);	
    		// });
            optional($this->getContextAirTime(), function ($airtime) use ($tag) {
                $tag->setAirTime($airtime);           
            });
    	});
    }

    protected function claimTag($stochastic)
    {
        $sociable = null;
        optional(Tag::whereCode($stochastic)->first(), function($tag) use (&$sociable) {
            // dd($this->getSociable()->mobile);
            $sociable = $this->getSociable();
            $sociable->upline()->associate($tag->tagger);
            $sociable->save();
            $tag->groups->each(function ($group) use ($sociable) {
                $sociable->assignGroup($group);
            });
            // $tag->roles->each(function ($role) use ($sociable) {
            //     $sociable->assignRole($role);
            // });
        });

        return $sociable;
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

    protected function getContextAirTime()
    {
        return AirTime::first();
    }

}
