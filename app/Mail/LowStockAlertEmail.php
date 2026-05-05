<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Low Stock Alert - SmartPOS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low_stock_alert',
        );
    }
}
