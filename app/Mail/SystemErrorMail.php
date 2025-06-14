<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Monolog\LogRecord;

class SystemErrorMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public array $details;

    /**
     * Create a new message instance.
     */
    public function __construct(LogRecord $record)
    {
        $this->details = [
            'level' => $record['level_name'],
            'message' => $record['message'],
            'time' => $record['datetime']->format('Y-m-d H:i:s T'),
            'url' => $record['context']['url'] ?? 'URL tapılmadı',
            'user_id' => $record['context']['user_id'] ?? 'Qonaq',
            'file' => $record['context']['file'] ?? 'Fayl tapılmadı',
            'line' => $record['context']['line'] ?? 'Sətir tapılmadı',
            'trace' => $record['context']['trace'] ?? 'Trace yoxdur',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = env('APP_NAME', 'Proweb');
        return new Envelope(
            subject: "Xəta: {$appName} Sistemində Kritik Xəta",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.system_error',
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
