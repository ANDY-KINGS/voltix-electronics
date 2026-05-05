<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $type; // e.g., 'low_stock'

    public function __construct($message, $type = 'info')
    {
        $this->message = $message;
        $this->type = $type;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin'),
        ];
    }
}
