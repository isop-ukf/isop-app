<?php

namespace App\Mail;

use App\Enums\InternshipStatus;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InternshipStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    private Internship $internship;
    private InternshipStatus $oldStatus;
    private InternshipStatus $newStatus;
    private string $note;
    private User $changedBy;
    private bool $recipiantIsStudent;

    /**
     * Create a new message instance.
     */
    public function __construct(Internship $internship, InternshipStatus $oldStatus, InternshipStatus $newStatus, string $note, User $changedBy, bool $recipiantIsStudent)
    {
        $this->internship = $internship;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->note = $note;
        $this->changedBy = $changedBy;
        $this->recipiantIsStudent = $recipiantIsStudent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Internship Status Updated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.internship.status_updated',
            with: [
                "internship" => $this->internship,
                "oldStatus" => $this->prettyStatus($this->oldStatus->value),
                "newStatus" => $this->prettyStatus($this->newStatus->value),
                "note" => $this->note,
                "recipiantIsStudent" => $this->recipiantIsStudent,
                "changedBy" => $this->changedBy,
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

    private function prettyStatus(string $status): string
    {
        return match ($status) {
            'SUBMITTED' => 'Zadané',
            'CONFIRMED_BY_COMPANY' => 'Potvrdená firmou',
            'CONFIRMED_BY_ADMIN' => 'Potvrdená garantom',
            'DENIED_BY_COMPANY' => 'Zamietnutá firmou',
            'DENIED_BY_ADMIN' => 'Zamietnutá garantom',
            'DEFENDED' => 'Obhájená',
            'NOT_DEFENDED' => 'Neobhájená',
            default => throw new \Exception("Invalid status: $status")
        };
    }
}
