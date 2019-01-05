<?php

namespace App\Notifications;

use App\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{EngageSparkChannel, EngageSparkMessage};

class CampaignAirTimeTransfer extends Notification
{
    use Queueable;

    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function via($notifiable)
    {
        return config('chatbot.notification.channels');
    }

    public function toArray($notifiable)
    {
        return [
            'mobile' => $notifiable->mobile,
            'air_time' => $this->campaign->air_time
        ];
    }

    public function toEngageSpark($notifiable)
    {
        return (new EngageSparkMessage())
            ->mode('topup')
            ->transfer($this->getAmount())
            ;
    }

    protected function getAmount()
    {
    	return $this->campaign->air_time;
    }
}
