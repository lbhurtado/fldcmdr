<?php

namespace App\Observers;

use App\SMS;
use App\Events\{SMSEvent, SMSEvents};

class SMSObserver
{
    public function created(SMS $sms)
    {
    	event(SMSEvents::CREATED, new SMSEvent($sms));
    }
}
