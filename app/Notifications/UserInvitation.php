<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\{TelerivetChannel, TelerivetMessage};
use NotificationChannels\Twilio\{TwilioChannel, TwilioSmsMessage};

class UserInvitation extends Notification
{
    use Queueable;

    protected $driver;
    
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    public function via($notifiable)
    {
        return [TwilioChannel::class];
        return [TelerivetChannel::class];
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()
            ->content($this->getContent($notifiable))
            ;
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content($this->getContent($notifiable))
            ;
    }

    protected function getContent($notifiable)
    {
        $name = $notifiable->user->name;
        $url = $this->getURL($notifiable);

        return trans('invite.notification', compact('name', 'url'));
    }

    protected function getURL($notifiable)
    {
        return config('chatbot.links.messenger')[in_array($this->driver, ['Telegram', 'Facebook']) ? $this->driver : 'Facebook'];
    }
}
