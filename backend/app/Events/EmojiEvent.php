<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmojiEvent implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return ['emoji-channel'];
    }
}
