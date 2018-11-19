<?php

namespace App;

use OTPHP\TOTP;
use App\Helpers\Phone;
use Illuminate\Notifications\Notifiable;
use App\Jobs\{RegisterTelerivetService, RequestOTP};
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $totp;

    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'driver', 'channel_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function routeNotificationForTelerivet()
    {
        return $this->telerivet_id;
    }

    public function registerTelerivet()
    {
        RegisterTelerivetService::dispatch($this);

        return $this;
    }

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

    public function scopeWithMobile($query, $value)
    {
        return $query->where('mobile', Phone::number($value));
    }
}
