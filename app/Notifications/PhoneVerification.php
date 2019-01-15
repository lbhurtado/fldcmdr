<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\{TelerivetChannel, TelerivetMessage};
use App\Channels\{EngageSparkChannel, EngageSparkMessage};
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
        // return config('chatbot.notification.channels');
        return array_merge(
            ['database'], config('chatbot.notification.send') 
                ? [config('chatbot.notification.default_channel')] 
                : []);
        return [EngageSparkChannel::class];
        return [TwilioChannel::class];
        return [TelerivetChannel::class];
    }

    public function toArray($notifiable)
    {
        return [
            'mobile'      => $notifiable->mobile,
            'verified_at' => $notifiable->verified_at,
        ];
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

    public function toEngageSpark($notifiable)
    {
        return (new EngageSparkMessage())
            ->content($this->content)
            ;
    }
}
