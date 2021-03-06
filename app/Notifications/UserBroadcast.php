<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{MessengerChannel, MessengerMessage};
use App\Channels\{EngageSparkChannel, EngageSparkMessage};

class UserBroadcast extends Notification
{
    use Queueable;

    protected $origin;

    protected $message;

    public function __construct($origin, $message)
    {
        $this->origin = $origin;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return config('chatbot.notification.channels');
        return [MessengerChannel::class];
    }

    public function toArray($notifiable)
    {
        return [
            'from' => $this->origin->mobile,
            'to' => $notifiable->mobile,
            'message' => $this->getContent($notifiable),
        ];
    }

    public function toMessenger($notifiable)
    {
        return MessengerMessage::create()
            ->content($this->message)
            ;
    }

    public function toEngageSpark($notifiable)
    {
        return (new EngageSparkMessage())
            ->content($this->getContent($notifiable))
            ;
    }

    protected function getContent($notifiable)
    {
        $from = $this->origin->name;
        $to = $notifiable->name;
        $message = $this->message;

        return trans('campaign.broadcast', compact('from', 'message' ,'to'));
    }
}
