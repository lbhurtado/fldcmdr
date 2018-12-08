<?php

namespace App\Events;

use App\SMS;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SMSEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $sms;

    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('sms-event');
    }

    public function getSMS()
    {
        return $this->sms;
    }
}
