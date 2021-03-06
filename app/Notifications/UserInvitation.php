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
        return config('chatbot.notification.channels');
        
        return [TwilioChannel::class];
        return [TelerivetChannel::class];
    }

    public function toArray($notifiable)
    {
        // return $notifiable->except(['created_at', 'updated_at']);
        return [
            'user_id' => $notifiable->user_id,
            'mobile' => $notifiable->mobile,
            'role' => $notifiable->role,
            'message' => $this->getContent($notifiable),
        ];
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
        $app = env('APP_NAME');
        $url = $this->getURL($notifiable);

        return trans('invite.notification', compact('name', 'app', 'url'));
    }

    protected function getURL($notifiable)
    {
        return config('chatbot.links.messenger')[in_array($this->driver, ['Telegram', 'Facebook']) ? $this->driver : 'Facebook'];
    }
}
