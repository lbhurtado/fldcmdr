<?php

namespace App\Notifications;

use App\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{EngageSparkChannel, EngageSparkMessage};

class CampaignInstruction extends Notification
{
    use Queueable;

    private $keyword;

    public function __construct($keyword)
    {
        $this->keyword = $keyword;
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
        $keyword = $this->keyword;

        return trans('campaign.instruction', compact('keyword'));
    }
}
