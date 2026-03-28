<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Mailable stub — full implementation in Phase 5
// Will use: resources/views/emails/orders/confirmation.blade.php
class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly mixed $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Подтверждение заказа',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.confirmation',
        );
    }
}
