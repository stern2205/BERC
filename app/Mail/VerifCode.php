<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifCode extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $name;

    /**
     * Create a new message instance.
     */
    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Get the message envelope (subject and from address).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Verification Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'auth.verif_code',
            with: [
                'code' => $this->code,
                'name' => $this->name,
            ]
        );
    }

    /**
     * No attachments needed.
     */
    public function attachments(): array
    {
        return [];
    }
}
