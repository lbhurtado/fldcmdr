<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\{TelerivetChannel, TelerivetMessage};

class AskableReward extends Notification
{
    use Queueable;

    protected $reward;

    protected $content;

    public function __construct($reward)
    {
        $this->reward = $reward;

        $this->content = trans('survey.reward');
    }

    public function via($notifiable)
    {
        return [TelerivetChannel::class];
    }

    public function toTelerivet($notifiable)
    {
        return TelerivetMessage::create()
            ->content($this->content)
            ->load(true)
            ;
    }
}
