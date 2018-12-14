<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{TelerivetChannel, TelerivetMessage};

class VerifiedAirTimeTransfer extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return [TelerivetChannel::class];
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()
        	->setCampaign('verified')
            ;
    }
}
