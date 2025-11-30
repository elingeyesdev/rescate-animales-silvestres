<?php

namespace App\Mail;

use App\Models\Veterinarian;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VeterinarianApplicationResponse extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Veterinarian $veterinarian,
        public bool $approved
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->approved 
            ? 'Solicitud de Veterinario Aprobada' 
            : 'Solicitud de Veterinario Rechazada';
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.veterinarian-application-response',
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
