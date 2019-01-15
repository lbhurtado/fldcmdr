<?php

namespace App;

use App\Eloquent\Phone;
use App\Contracts\Sociable;
use Illuminate\Support\Arr;
use App\{User, Contact, Area, Campaign};
use App\Jobs\{SendCampaign, SendInstruction, SendFeedback, SendAdhoc};

class Command
{
	private $user;

	private $sociable;

    private $group;

    private $area;

    private $campaign;

    private $count;

    private $contacts;

    private $cmd;

    private $status;

    private $commander;

    private $message;

    private $report = false;

    private $keyword;

    public static function tag($mobile, $attributes = [])
    {
        $cmd = __FUNCTION__;
    	//improve on this
    	$commander = User::findByMobile($mobile) ?? Contact::findByMobile($mobile);
        
        if ($commander instanceof Sociable) {
            optional(new static($commander), function ($command) use ($commander, $attributes, $cmd, &$tag) {
                $area = Arr::get($attributes, 'area');
                $group = Arr::get($attributes, 'group');
                $campaign = Arr::get($attributes, 'campaign');
                $stochastic = Arr::get($attributes, 'keyword');

                $tag = $command
                    ->setCmd($cmd)
                    ->setCommander($commander)
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
        $cmd = __FUNCTION__;

        $keyword = Arr::get($attributes, 'keyword');
        $name = Arr::get($attributes, 'name') ?? 'Anonymous';


        if (! Tag::validateCode($keyword)) return;
    
        $commander = Contact::firstOrCreate(compact('mobile'), compact('mobile', 'name'));

        return optional(new static($commander), function ($command) use ($commander, $cmd, $keyword) {

            $command
                    ->setCmd($cmd)
                    ->setCommander($commander)
                    ->setKeyword($keyword)
                    ->tagclaim()
                    // ->setCommanderArea()
                    ->setStatus('ok')
                    ;

            return $command;
        });
        // return (new static($commander))->claimTag($keyword);
    }

    public static function pick($mobile, $arguments)
    {
        $commander = User::findByMobile($mobile) ?? Contact::findByMobile($mobile);

        if ($commander instanceof Sociable) {

            $cmd = __FUNCTION__;
            
            return optional(new static($commander), function ($command) use ($commander, $cmd, $arguments) {

                $count = Arr::get($arguments, 'count', 1);
                $campaign = Arr::get($arguments, 'campaign'); 

                return $command
                    ->setCmd($cmd)
                    ->setCommander($commander)
                    ->setContextCampaign($campaign)
                    ->generateRandomContactsList($count)
                    ->campaign()
                    ->setStatus('ok')
                    ;

            })->report();
        }
    }

    public static function broadcast($mobile, $arguments)
    {
        $commander = User::findByMobile($mobile) ?? Contact::findByMobile($mobile);

        if ($commander instanceof Sociable) {

            $cmd = __FUNCTION__;
            
            return optional(new static($commander), function ($command) use ($commander, $cmd, $arguments) {

                $message = Arr::get($arguments, 'message');

                return $command
                    ->setCmd($cmd)
                    ->setCommander($commander)
                    ->setMessage($message)
                    ->generateContactsList()
                    ->send()
                    ->setStatus('ok')
                    ;

            })->report();
        }
    }

    public static function group($mobile, $arguments)
    {
        $commander = User::findByMobile($mobile) ?? Contact::findByMobile($mobile);

        if ($commander instanceof Sociable) {

            $cmd = __FUNCTION__;
            
            return optional(new static($commander), function ($command) use ($commander, $cmd, $arguments) {

                $group = Arr::get($arguments, 'group');

                return $command
                    ->setCmd($cmd)
                    ->setCommander($commander)
                    ->setContextGroup($group)
                    ->setCommanderGroup()
                    ->setStatus('ok')
                    ;

            })->report();
        }
    }

    public static function area($mobile, $arguments)
    {
        $commander = User::findByMobile($mobile) ?? Contact::findByMobile($mobile);

        if ($commander instanceof Sociable) {

            $cmd = __FUNCTION__;
            
            return optional(new static($commander), function ($command) use ($commander, $cmd, $arguments) {

                $area = Arr::get($arguments, 'area');

                return $command
                    ->setCmd($cmd)
                    ->setCommander($commander)
                    ->setContextArea($area)
                    ->setCommanderArea()
                    ->setStatus('ok')
                    ;

            })->report();
        }
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

        // if ($code == 'ambacan') {
        \Log::info(compact('code'));
        // }
    	return tap(Tag::createWithTagger(compact('code'), $sociable), function ($tag) {
    		optional($this->getContextGroup(), function ($group) use ($tag) {
                \Log::info("createTag.group = {$group->name}");
    			$tag->setGroup($group);    			
    		});
            optional($this->getContextArea(), function ($area) use ($tag) {
                \Log::info("createTag.area = {$area->name}");
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

        optional(Tag::withCode($stochastic), function($tag) use (&$sociable, $stochastic) {
            $sociable = $this->getSociable();

            $sociable->upline()->associate($tag->tagger);
            $sociable->save();

            $tag->groups->each(function ($group) use ($sociable) {
                $sociable->assignGroup($group);
            });
            
            $tag->areas->each(function ($area) use ($sociable) {
                $sociable->assignArea($area);
            });

            if (! $sociable->wasRecentlyCreated) {
                $campaign = Campaign::whereName('ulit')->first();
                SendCampaign::dispatch($sociable, $campaign);
            }
            else {
                $tag->campaigns->each(function ($campaign) use ($sociable) {
                    SendCampaign::dispatch($sociable, $campaign);    
                });
            }

            //this is working
            //disabled for now            
            tap(static::tag($sociable->mobile, ['keyword' => $stochastic . '_',]), function ($tag) use ($sociable) {
                SendInstruction::dispatch($sociable, $tag->code);
            });
        });

        return $sociable;
    }

    protected function tagclaim()
    {
        $this->claimTag($this->getKeyword());

        return $this;
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
    	return $this->group 
            ?? $this->getSociable()->groups()->latest()->first()
            ?? optional(optional($this->getSociable()->upline)->groups())->first()
            ;
    }

    //improve on this
    protected function getContextArea()
    {
        return $this->area 
            ?? $this->getSociable()->areas()->latest()->first()
            ?? optional(optional($this->getSociable()->upline)->areas())->first()
            ;
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
        if (is_numeric($name)) {
            optional(Group::find($name), function ($group) {
                $this->group = $group;
            });
        }
        else {
            optional(Group::withName($name), function ($group) {
                $this->group = $group;
            });
        }

        return $this;
    }

    protected function setContextArea($name)
    {
        if (is_numeric($name)) {
            optional(Area::find($name), function ($area) {
                $this->area = $area;
            });
        }
        else {
            optional(Area::withName($name), function ($area) {
                $this->area = $area;
            });
        }

        return $this;
    }

    protected function setContextCampaign($name)
    {
        optional(Campaign::whereName($name)->first(), function ($campaign) {
            $this->campaign = $campaign;
        });

        return $this;
    }

    protected function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    protected function generateContactsList()
    {
        //improve this
        //get only downlines

        $this->contacts = Contact::all()->shuffle();

        return $this;
    }

    protected function generateRandomContactsList($count)
    {
        //improve this
        //get only downlines

        $this->contacts = Contact::all()->random($this->count = $count);

        return $this;
    }

    protected function campaign()
    {
        $campaign = $this->getContextCampaign();

        if (! $campaign->disabled) {
            $this->contacts->each(function ($contact) use ($campaign) {
                SendCampaign::dispatch($contact, $campaign);
            });
            $this->report = true;
        }
        $campaign->disabled = true;
        $campaign->save();

        return $this;
    }

    protected function send()
    {
        $this->contacts->each(function ($contact) {
            SendAdhoc::dispatch($this->commander, $contact, $this->message);
        });

        return $this;
    }

    protected function setCmd($cmd)
    {
        $this->cmd = $cmd;

        return $this;
    }

    protected function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getCommander()
    {
        return $this->commander;
    }

    protected function setCommander(Sociable $commander)
    {
        $this->commander = $commander;

        return $this;
    }    

    protected function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }    

    protected function setCommanderGroup()
    {
        optional($this->commander, function ($commander) {
            optional($this->group, function ($group) use ($commander) {
                $commander->syncGroups($group);
            });
        });

        return $this;
    }

    protected function setCommanderArea()
    {
        optional($this->commander, function ($commander) {
            optional($this->area, function ($area) use ($commander) {
                $commander->syncAreas($area);
            });
        });

        return $this;
    }

    protected function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    protected function getKeyword()
    {
        return $this->keyword;
    }

    protected function report()
    {
        switch ($this->cmd) {
             case 'pick':
                 if ($this->status == 'ok') {

                    if ($this->report == false)
                        return;

                    $this->contacts->each(function($contact) use (&$list) {
                        $mobile = Phone::number($contact->mobile, 3);
                        $list .= "{$contact->name} {$mobile}\n";
                    });
                    $msg = implode("\n", [
                                ucwords($this->campaign->name) . ' List:',
                                $list,
                            ]);

                    SendFeedback::dispatch($this->commander, $msg);                        
                 }

                 break;

             case 'group':
                if ($this->status == 'ok') {
                    $msg = trans('campaign.assignment.group', [
                        'group' => $this->commander->groups->first()->name
                    ]);

                    // return $this->group;
                    SendFeedback::dispatch($this->commander, $msg); 
                }

                break;
             
             case 'area':
                if ($this->status == 'ok') {
                    $msg = trans('campaign.assignment.area', [
                        'area' => $this->commander->areas->first()->qn
                    ]);

                    SendFeedback::dispatch($this->commander, $msg); 
                }

                break;

             default:
                 # code...
                 // return $this;
         }

         return $this;
    }
}
