<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $purpose = 'verify'
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->purpose === 'reset'
            ? 'Your Scoop password reset code'
            : 'Your Scoop verification code';

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address('noreply@ntsarcus.com', 'Scoop'),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->htmlBody(),
            textString: $this->textBody(),
        );
    }

    private function textBody(): string
    {
        if ($this->purpose === 'reset') {
            return "Your Scoop password reset code is: {$this->code}\n\nThis code expires in 15 minutes.";
        }

        return "Your Scoop verification code is: {$this->code}\n\nThis code expires in 15 minutes.";
    }

    private function htmlBody(): string
    {
        $title = $this->purpose === 'reset' ? 'Password reset' : 'Email verification';
        $intro = $this->purpose === 'reset'
            ? 'Use this code to reset your Scoop password:'
            : 'Use this code to verify your Scoop account:';

        $code = e($this->code);

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:24px; color:#0f172a;">
  <div style="max-width:480px; margin:0 auto; background:#ffffff; border-radius:12px; padding:28px; border:1px solid #e2e8f0;">
    <h1 style="margin:0 0 8px; font-size:20px;">{$title}</h1>
    <p style="margin:0 0 20px; color:#64748b; font-size:14px;">{$intro}</p>
    <p style="margin:0 0 20px; font-size:32px; letter-spacing:8px; font-weight:700; text-align:center; color:#0A9D9B;">{$code}</p>
    <p style="margin:0; color:#94a3b8; font-size:12px;">This code expires in 15 minutes. If you did not request this, you can ignore this email.</p>
  </div>
</body>
</html>
HTML;
    }
}
