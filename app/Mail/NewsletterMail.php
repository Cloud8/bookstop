<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Mailable stub — full implementation in Phase 8
// Will use: resources/views/emails/newsletter/campaign.blade.php
class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $mailSubject,
        public readonly string $body,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter.campaign',
        );
    }
}
