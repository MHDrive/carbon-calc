<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
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

    public $messages;
    public $submissions;
    public $condition;

    /**
     * Create a new message instance.
     */
    public function __construct(array $messages, Submission $submissions = null, $condition = false)
    {
        $this->messages = $messages;
        $this->submissions = $submissions;
        $this->condition = $condition;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->messages['subject'] ?? 'Submission Notification',
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
    // public function attachments(): array
    // {
    //     return [];
    // }
    public function attachments(): array
    {
        $attachments = [];
        if ($this->condition) {
            return $attachments;
        }
        // Periksa apakah $this->submissions adalah objek dan memiliki metode getFirstMedia
        if (is_object($this->submissions) && method_exists($this->submissions, 'getMedia')) {
            // Ambil semua media dari koleksi 'file_submissions'
            $mediaCollection = $this->submissions->getMedia('file_submissions');

            foreach ($mediaCollection as $media) {
                $mediaPath = $media->getPath(); // Dapatkan path asli file

                // Debugging
                Log::info('Media Path: ' . $mediaPath);

                if (file_exists($mediaPath)) {
                    $attachments[] = Attachment::fromPath($mediaPath)
                        ->as($media->file_name)
                        ->withMime($media->mime_type);
                } else {
                    Log::error('File does not exist at path: ' . $mediaPath);
                }
            }

            // Jika tidak ada media ditemukan, log pesan error
            if (empty($attachments)) {
                Log::error('No media found for the specified collection.');
            }
        } else {
            Log::error('Invalid submissions object or method getMedia does not exist.');
        }

        return $attachments;
    }
}
