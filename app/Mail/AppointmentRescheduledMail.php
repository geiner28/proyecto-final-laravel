<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentRescheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;
    public string $oldStart;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, string $oldStart)
    {
        $this->appointment = $appointment;
        $this->oldStart = $oldStart;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cita Reagendada - ' . $this->appointment->doctor->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-rescheduled',
            with: [
                'appointment' => $this->appointment,
                'doctorName' => $this->appointment->doctor->name,
                'doctorSpecialty' => $this->appointment->doctor->specialty,
                'patientName' => $this->appointment->patient_name,
                'oldStart' => $this->oldStart,
                'newStart' => $this->appointment->start_at,
            ],
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
