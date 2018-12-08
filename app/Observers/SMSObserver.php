<?php

namespace App\Observers;

use App\SMS;
use App\Events\{SMSEvent, SMSEvents};

class SMSObserver
{
    public function creating(SMS $sms)
    {
    	event(SMSEvents::CREATING, new SMSEvent($sms));
    }

    public function created(SMS $sms)
    {
    	event(SMSEvents::CREATED, new SMSEvent($sms));
    }
}
