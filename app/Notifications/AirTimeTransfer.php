<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{TelerivetChannel, TelerivetMessage};
use App\Channels\{EngageSparkChannel, EngageSparkMessage};

abstract class AirTimeTransfer extends Notification
{
    use Queueable;

    public static function invoke($campaign)
    {
        $notification = config("chatbot.campaigns.{$campaign}.notification");

        return new $notification;
    }

    public function via($notifiable)
    {
        return [EngageSparkChannel::class];
        return [TelerivetChannel::class];
    }

    public function toArray($notifiable)
    {
        return [
            'mobile' => $notifiable->mobile,
            'campaign' => $this->getCampaign(),
        ];
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()->setCampaign($this->getCampaign());
    }

    public function toEngageSpark($notifiable)
    {
        return (new EngageSparkMessage())
            ->mode('airtime')
            ->campaign($this->getCampaign())
            ;
    }

    abstract protected function getCampaign();
}
