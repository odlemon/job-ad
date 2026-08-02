<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteApplicantMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $applicantName,
        public string $jobTitle,
        public string $companyName,
        public int $jobId
    ) {
    }

    public function build(): self
    {
        $applyUrl = url('/jobs/' . $this->jobId);

        return $this
            ->from(config('mail.from.address', 'noreply@kyntaro.com'), config('mail.from.name', 'Scoop'))
            ->subject("You're invited to apply: {$this->jobTitle} at {$this->companyName}")
            ->view('emails.invite-applicant', [
                'applicantName' => $this->applicantName,
                'jobTitle' => $this->jobTitle,
                'companyName' => $this->companyName,
                'applyUrl' => $applyUrl,
            ]);
    }
}
