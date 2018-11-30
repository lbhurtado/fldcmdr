<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{MessengerChannel, MessengerMessage};

class UserBroadcast extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return [MessengerChannel::class];
    }

    public function toMessenger($notifiable)
    {
        return MessengerMessage::create()
            ->content($this->message)
            ;
    }
}
