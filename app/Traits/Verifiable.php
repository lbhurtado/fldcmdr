<?php

namespace App\Traits;

use OTPHP\TOTP;
use App\Jobs\RequestOTP;

trait Verifiable
{

    protected $totp;

    public function challenge()
    {
        $this->totp = TOTP::create(null, 360);

        RequestOTP::dispatch($this, $this->totp->now());
    }

    public function verify($otp, $notSimulated = true)
    {
        $verified = ! $notSimulated || $this->totp->verify($otp);

        if ($verified) $this->forceFill(['verified_at' => now()])->save(); 

        return $this;
    }

    public function isVerified()
    {
        return $this->verified_at && $this->verified_at <= now();
    } 
}
