<?php

namespace App\Mail\Authentication;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActiveMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $activeToken;
    private string $url = 'active_account/';

    /**
     * Create a new message instance.
     */
    public function __construct(string $activeToken)
    {
        $this->activeToken = $activeToken;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Active Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'authentication.active_mail',
            with: [
                'frontend_host' => config('app.frontend_host') . $this->url,
                'activeToken' => $this->activeToken,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
