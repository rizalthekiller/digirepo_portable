<?php

namespace App\Mail;

use App\Models\Thesis;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class ThesisApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $thesis;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Thesis $thesis, $pdfPath = null)
    {
        $this->thesis = $thesis;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Skripsi Disetujui - ' . $this->thesis->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.theses.approved',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as('Sertifikat_Unggah_' . $this->thesis->user->nim . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }
        
        return [];
    }
}
