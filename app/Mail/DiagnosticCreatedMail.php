<?php

namespace App\Mail;

use App\Models\Diagnostic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DiagnosticCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $diagnostic;

    /**
     * Create a new message instance.
     */
    public function __construct(Diagnostic $diagnostic)
    {
        $this->diagnostic = $diagnostic;
        $this->diagnostic->load('doctor');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📋 Diagnóstico Médico Disponible - MediCitas',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.diagnostic-created',
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
