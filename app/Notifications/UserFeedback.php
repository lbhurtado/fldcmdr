<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{MessengerChannel, MessengerMessage};
use App\Channels\{EngageSparkChannel, EngageSparkMessage};

class UserFeedback extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return config('chatbot.notification.channels');
    }

    public function toMessenger($notifiable)
    {
        return MessengerMessage::create()
            ->content($this->message)
            ;
    }

    public function toArray($notifiable)
    {
        return [
            'mobile' => $notifiable->mobile,
            'message' => $this->getContent($notifiable),
        ];
    }

    public function toEngageSpark($notifiable)
    {
        return (new EngageSparkMessage())
            ->content($this->getContent($notifiable))
            ;
    }

    protected function getContent($notifiable)
    {
        $name = $notifiable->name;
        $message = $this->message;

        return trans('campaign.feedback', compact('message', 'name'));
    }
}
