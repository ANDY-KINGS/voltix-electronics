<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order_id;
    public $payment_status;
    public $mpesa_code;
    public $amount;

    public function __construct($order_id, $payment_status, $mpesa_code, $amount)
    {
        $this->order_id = $order_id;
        $this->payment_status = $payment_status;
        $this->mpesa_code = $mpesa_code;
        $this->amount = $amount;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.' . $this->order_id),
        ];
    }
}
