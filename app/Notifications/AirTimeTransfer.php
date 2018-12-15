<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{TelerivetChannel, TelerivetMessage};

abstract class AirTimeTransfer extends Notification
{
    use Queueable;

    private $campaign;

    public static function invoke($campaign)
    {
        $class = config("chatbot.campaigns.{$campaign}.notification");

        return new $class;
    }

    public function via($notifiable)
    {
        return [TelerivetChannel::class];
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()
        	->setCampaign($this->getCampaign())
            ;
    }

    abstract protected function getCampaign();
}
