<?php

namespace App\Channels;

use BotMan\BotMan\BotMan;
use Illuminate\Notifications\Notification;

class MessengerChannel
{
    protected $bot;

    public function __construct(BotMan $bot)
    {
        $this->bot = $bot;
    }

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toMessenger($notifiable);
        $messenger = $notifiable->routeNotificationFor('messenger');
        $this->getBot()->say($message->content, $messenger['channel_id'], $messenger['driver']);

        return true;
    }

    protected function getBot()
    {
        return $this->bot;
    }
}