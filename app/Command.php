<?php

namespace App;

use App\Jobs\SendCampaign;
use App\Contracts\Sociable;
use Illuminate\Support\Arr;
use App\Notifications\CampaignMessage;
use App\{User, Contact, Area, Campaign};

class Command
{
	private $user;

	private $sociable;

    private $group;

    private $area;

    private $campaign;

    public static function tag($mobile, $attributes = [])
    {
        $area = Arr::get($attributes, 'area');
        $group = Arr::get($attributes, 'group');
        $campaign = Arr::get($attributes, 'campaign');
        $stochastic = Arr::get($attributes, 'keyword');
    	//improve on this
    	$sociable = User::findByMobile($mobile) ?? Contact::findByMobile($mobile);

    	return ! $sociable || optional(new static($sociable), function ($command) use ($group, $area, $campaign, $stochastic) {

            $command
                ->setContextGroup($group)
                ->setContextArea($area)
                ->setContextCampaign($campaign)
                ->createTag($stochastic);       
        });
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
            optional($this->getContextArea(), function ($area) use ($tag) {
                $tag->setArea($area);             
            });
    		// optional($this->getContextRole(), function ($role)  use ($tag) {
    		// 	$tag->setRole($role);	
    		// });
            optional($this->getContextCampaign(), function ($airtime) use ($tag) {
                $tag->setCampaign($airtime);           
            });
    	});
    }

    protected function claimTag($stochastic)
    {
        $sociable = null;
        optional(Tag::whereCode($stochastic)->first(), function($tag) use (&$sociable) {
            $sociable = $this->getSociable();
            $sociable->upline()->associate($tag->tagger);
            $sociable->save();
            $tag->groups->each(function ($group) use ($sociable) {
                $sociable->assignGroup($group);
            });
            $tag->areas->each(function ($area) use ($sociable) {
                $sociable->assignArea($area);
            });
            $tag->campaigns->each(function ($campaign) use ($sociable) {
                SendCampaign::dispatch($sociable, $campaign);
                // $sociable->notify(new CampaignMessage($campaign));
                // $sociable->notify(new CampaignAirTimeTransfer($campaign));
            });
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
    	return $this->group ?? $this->getSociable()->groups()->latest()->first();
    }

    //improve on this
    protected function getContextArea()
    {
        return $this->area ?? $this->getSociable()->areas()->latest()->first();
    }

    //improve on this
    protected function getContextRole()
    {
    	return $this->getSociable()->roles()->latest()->first();
    }

    //improve on this
    protected function getContextCampaign()
    {
        return $this->campaign ?? Campaign::first();
    }

    protected function setContextGroup($name)
    {
        optional(Group::whereName($name)->first(), function ($group) {
            $this->group = $group;
        });

        return $this;
    }

    protected function setContextArea($name)
    {
        optional(Area::whereName($name)->first(), function ($area) {
            $this->area = $area;
        });

        return $this;
    }

    protected function setContextCampaign($name)
    {
        optional(Campaign::whereName($name)->first(), function ($campaign) {
            $this->campaign = $campaign;
        });

        return $this;
    }
}
