<?php

namespace App\Traits;

use OTPHP\TOTP;
use App\Events\{UserEvent, UserEvents};
use App\Jobs\{RequestOTP, VerificationOfDownline};

trait Verifiable
{

    protected $totp;

    protected $expiration = 60;

    public function challenge()
    {
        $this->totp = TOTP::create(null, 360);

        RequestOTP::dispatch($this, $this->totp->now());
    }

    public function verify($otp, $notSimulated = true)
    {
        $verified = ! $notSimulated || $this->totp->verify($otp);

        if ($verified) {
            $this->forceFill(['verified_at' => now()])->save(); 
            
            event(UserEvents::VERIFIED, new UserEvent($this));
        }

        return $this;
    }

    public function isVerified()
    {
        return $this->verified_at && $this->verified_at <= now();
    } 

    public function isNotVerified()
    {
        return ! $this->isVerified();
    }

    public function isVerificationStale()
    {
        return $this->verified_at && $this->verified_at->addSeconds($this->expiration) <= now();
    }

    public function notifyVerificationOfDownline($downline)
    {
        VerificationOfDownline::dispatch($this, $downline);
        
    }

    public function scopeVerified($query)
    {
        return $query->whereDate('verified_at', '=', now()->toDateString());
    }
}
