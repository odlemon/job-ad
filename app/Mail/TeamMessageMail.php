<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $senderName,
        public string $companyName,
        public string $subjectLine,
        public string $body
    ) {
    }

    public function build(): self
    {
        return $this
            ->from(config('mail.from.address', 'noreply@kyntaro.com'), config('mail.from.name', 'Scoop'))
            ->subject($this->subjectLine)
            ->view('emails.team-message', [
                'recipientName' => $this->recipientName,
                'senderName' => $this->senderName,
                'companyName' => $this->companyName,
                'body' => $this->body,
            ]);
    }
}
