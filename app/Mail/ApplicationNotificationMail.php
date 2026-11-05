<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string[]  $lines  Absätze der Nachricht
     * @param  array{name: string, data: string}|null  $pdf  optionaler Bescheid als PDF
     * @param  string|null  $deciderName  Name der Person, die den Antrag entschieden hat
     */
    public function __construct(
        public string $mailSubject,
        public string $greetingName,
        public array $lines,
        public ?array $pdf = null,
        public ?string $deciderName = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.application-notification');
    }

    public function attachments(): array
    {
        return $this->pdf
            ? [Attachment::fromData(fn () => $this->pdf['data'], $this->pdf['name'])->withMime('application/pdf')]
            : [];
    }
}
