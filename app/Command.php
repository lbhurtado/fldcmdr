<?php

namespace App;

use App\User;

class Command
{
	private $mobile;

    public static function tag($mobile, $code = null)
    {
    	return (new static($mobile))->group($code);
    }

    public function __construct($mobile)
    {
    	$this->mobile = $mobile;
    }

    public function group($code = null)
    {
    	$code = $code ?? str_random(6);
    	tap($this->getContextGroup(), function ($group) use (&$code) {
    		$code = $group->tags()->create(compact('code'))->code;
    	})->save();

    	return $code;
    }

    protected function getUser()
    {
    	return User::withMobile($this->mobile)->firstOrFail();
    }

    protected function getContextGroup()
    {
    	return $this->getUser()->groups()->latest()->first();
    }

}
