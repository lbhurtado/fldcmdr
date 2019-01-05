<?php

namespace App\Notifications;

use App\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{EngageSparkChannel, EngageSparkMessage};

class CampaignMessage extends Notification
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
            'message' => $this->getContent(),
        ];
    }

    public function toEngageSpark($notifiable)
    {
        return (new EngageSparkMessage())
            ->content($this->getContent())
            ;
    }

    protected function getContent()
    {
        $message = $this->campaign->message;
        $air_time = $this->campaign->air_time;

        return trans('campaign.onboarding', compact('message', 'air_time'));
    }
}
