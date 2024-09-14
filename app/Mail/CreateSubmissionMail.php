<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateSubmissionMail extends Mailable implements ShouldQueue
// implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $details;
    public $submissions;

    /**
     * Create a new message instance.
     */
    public function __construct(array $details, string $submissions = null)
    {
        $this->details = $details;
        $this->submissions = $submissions;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->details['subject'] ?? 'Submission Notification',
            from: new Address(config('mail.from.address'), config('mail.from.name')),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.create-submission', // Menggunakan markdown view
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        // Dapatkan path dari media pertama yang tersimpan
        // Pastikan $submissions adalah objek yang valid
        if (is_object($this->submissions) && method_exists($this->submissions, 'getFirstMedia')) {
            if ($media = $this->submissions->getFirstMedia('file_submissions')) {
                $mediaPath = $media->getPath(); // Dapatkan path asli file

                // Tambahkan file sebagai attachment
                $attachments[] = Attachment::fromPath($mediaPath)
                    ->as($media->file_name)
                    ->withMime($media->mime_type);
            }
        }
        return $attachments;
    }
}
