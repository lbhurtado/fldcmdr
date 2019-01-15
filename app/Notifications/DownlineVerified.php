<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\{MessengerChannel, MessengerMessage};
use App\Channels\{EngageSparkChannel, EngageSparkMessage};

class DownlineVerified extends Notification
{
    use Queueable;

    private $downline;

    public function __construct(User $downline)
    {
        $this->downline = $downline;
    }

    public function via($notifiable)
    {
        return array_merge(
            ['database'], 
            config('chatbot.notification.send') 
                ? [config('chatbot.notification.default_channel')] 
                : []);
        return ['database', MessengerChannel::class];
    }

    public function toArray($notifiable)
    {
        return [
            'mobile' => $this->downline->mobile,
            'message' => $this->getContent($notifiable),
        ];
    }

    public function toMessenger($notifiable)
    {
        return MessengerMessage::create()
            ->content($this->getContent($notifiable))
            ;
    }

    protected function getContent($notifiable)
    {
        return trans('verify.notify', [
            'mobile' => $this->downline->mobile,
        ]);
    }

    public function toEngageSpark($notifiable)
    {
        return (new EngageSparkMessage())
            ->content($this->getContent($notifiable))
            ;
    }
}
