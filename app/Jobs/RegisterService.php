<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

abstract class RegisterService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $field;

    protected $proto;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {        
        $this->user = $user;

        $util = \libphonenumber\PhoneNumberUtil::getInstance();

        $this->proto = $util->parse($this->user->mobile, "PH");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $authy_id = $this->getId();

        tap($this->getUser(), function($user) {
            $user->forceFill(compact($this->getField()));
        })->save();
    }

    abstract public function getId();

    protected function getUser()
    {
        return $this->user;
    }

    protected function getField()
    {
        return $this->field;
    }

    protected function getCountryCode()
    {
        return $this->proto->getCountryCode();
    }

    protected function getNumber()
    {
        return $this->proto->getNationalNumber();
    }

    protected function getEmail()
    {
        return $this->getCountryCode() . $this->getNumber() . "@serbis.io";   
    }
}
