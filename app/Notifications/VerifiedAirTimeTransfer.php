<?php

namespace App\Notifications;

class VerifiedAirTimeTransfer extends AirTimeTransfer
{
    protected function getCampaign()
    {
        return 'verified';
    }
}
