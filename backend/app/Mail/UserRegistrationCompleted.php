<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistrationCompleted extends Mailable
{
    use Queueable, SerializesModels;

    private string $name;
    private string $activation_token;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $activation_token)
    {
        $this->name = $name;
        $this->activation_token = $activation_token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ISOP] Účet vytvorený',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.registration.completed',
            with: [
                "name" => $this->name,
                "activation_token" => $this->activation_token
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
