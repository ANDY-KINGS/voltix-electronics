<?php

namespace App\Mail;

use App\Models\WarrantyClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WarrantyStatusEmail extends Mailable
{
    use Queueable, SerializesModels;

    public WarrantyClaim $claim;

    public function __construct(WarrantyClaim $claim)
    {
        $this->claim = $claim;
    }

    public function envelope(): Envelope
    {
        $productName = $this->claim->orderItem->product->name ?? 'Your Product';
        return new Envelope(
            subject: 'Warranty Claim Update — ' . $productName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.warranty-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
