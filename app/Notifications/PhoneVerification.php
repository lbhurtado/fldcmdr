<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\{TelerivetChannel, TelerivetMessage};
use NotificationChannels\Twilio\{TwilioChannel, TwilioSmsMessage};

class PhoneVerification extends Notification
{
    use Queueable;


    protected $content;

    public function __construct($otp)
    {
        $this->content = trans('verify.challenge', compact('otp'));
    }

    public function via($notifiable)
    {
        return [TwilioChannel::class];
        return [TelerivetChannel::class];
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()
            ->content($this->content)
            ;
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content($this->content)
            ;
    }
}
