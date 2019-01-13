<?php

namespace App\Events;

use App\Tag;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TagEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('tag-event');
    }

    public function getTag()
    {
        return $this->tag;
    }
}
