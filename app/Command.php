<?php

namespace App;

use App\Contracts\Sociable;
use Illuminate\Support\Arr;
// use App\Notifications\CampaignMessage;
use App\{User, Contact, Area, Campaign};
use App\Jobs\{SendCampaign, SendInstruction};

class Command
{
	private $user;

	private $sociable;

    private $group;

    private $area;

    private $campaign;

    public static function tag($mobile, $attributes = [])
    {
    	//improve on this
    	$sociable = User::findByMobile($mobile) ?? Contact::findByMobile($mobile);

        if ($sociable instanceof Sociable) {
            optional(new static($sociable), function ($command) use ($attributes, &$tag) {
                $area = Arr::get($attributes, 'area');
                $group = Arr::get($attributes, 'group');
                $campaign = Arr::get($attributes, 'campaign');
                $stochastic = Arr::get($attributes, 'keyword');

                $tag = $command
                    ->setContextGroup($group)
                    ->setContextArea($area)
                    ->setContextCampaign($campaign)
                    ->createTag($stochastic);       
            });

            return $tag;
        }
    }


    public static function claim($mobile, $attributes)
    {
        //improve on this
        $keyword = Arr::get($attributes, 'keyword');
        $name = Arr::get($attributes, 'name') ?? 'Anonymous';

        $sociable = Contact::firstOrCreate(compact('mobile', 'name'));

        return (new static($sociable))->claimTag($keyword);
    }

    public function __construct(Sociable $sociable)
    {
    	$this->sociable = $sociable;
    }

    protected function createTag($stochastic = null)
    {
    	$code = $this->generateCode($stochastic);

        if (Tag::whereCode($code)->first())
            $code = $this->generateCode($stochastic);
        if (Tag::whereCode($code)->first())
            $code = $this->generateCode($stochastic);
        if (Tag::whereCode($code)->first())
            $code = $this->generateCode($stochastic);
        if (Tag::whereCode($code)->first())
            $code = $this->generateCode($stochastic);

    	$sociable = $this->getSociable();

    	return tap(Tag::createWithTagger(compact('code'), $sociable), function ($tag) {
    		optional($this->getContextGroup(), function ($group) use ($tag) {
    			$tag->setGroup($group);    			
    		});
            optional($this->getContextArea(), function ($area) use ($tag) {
                $tag->setArea($area);             
            });
            optional($this->getContextCampaign(), function ($campaign) use ($tag) {
                $tag->setCampaign($campaign);           
            });
    	});
    }

    protected function claimTag($stochastic)
    {
        $sociable = null;

        optional(Tag::whereCode($stochastic)->first(), function($tag) use (&$sociable, $stochastic) {
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
            });
            tap(static::tag($sociable->mobile, ['keyword' => $stochastic . '_',]), function ($tag) use ($sociable) {
                SendInstruction::dispatch($sociable, $tag->code);
            });
        });

        return $sociable;
    }

    protected function generateCode($seed = null)
    {
        $seed = $seed ?? str_random(6); 

        $seed = preg_replace('/[0-9]+/', '', $seed);
        if ($seed[-1] == '_') {
            $seed = substr($seed, 0, -1);  

            return $seed . rand(100, 999);
        }
        else
            return $seed;
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
