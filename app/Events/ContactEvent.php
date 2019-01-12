<?php

namespace App\Events;

use App\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContactEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $contact;

    private $area;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('contact-event');
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    public function getArea()
    {
        return $this->area;
    }
}
