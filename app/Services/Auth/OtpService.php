<?php

namespace App\Services\Auth;

use App\Mail\VerificationOtpMail;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function send(string $email, string $purpose = 'verify'): string
    {
        $code = (string) random_int(100000, 999999);

        EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->delete();

        EmailOtp::create([
            'email' => $email,
            'code' => $code,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(15),
        ]);

        // Always log in local/dev for debugging
        Log::info('Scoop OTP generated', [
            'email' => $email,
            'purpose' => $purpose,
            'code' => $code,
        ]);

        try {
            $this->deliver($email, $code, $purpose);
            Log::info('Scoop OTP email sent', ['email' => $email, 'purpose' => $purpose]);
        } catch (\Throwable $e) {
            Log::error('OTP email send failed', [
                'email' => $email,
                'purpose' => $purpose,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        return $code;
    }

    public function verify(string $email, string $code, string $purpose = 'verify'): bool
    {
        $otp = EmailOtp::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $otp || ! $otp->isValid() || ! hash_equals($otp->code, $code)) {
            return false;
        }

        $otp->update(['consumed_at' => now()]);

        return true;
    }

    /**
     * Prefer ZeptoMail HTTP API (same path as invite emails). Fall back to SMTP mailer.
     */
    private function deliver(string $email, string $code, string $purpose): void
    {
        $fromAddress = (string) config('mail.from.address', 'noreply@ntsarcus.com');
        $fromName = (string) config('mail.from.name', 'Scoop');
        $apiToken = (string) config('mail.mailers.smtp.password');

        $mailable = new VerificationOtpMail($code, $purpose);
        $subject = $purpose === 'reset'
            ? 'Your Scoop password reset code'
            : 'Your Scoop verification code';

        $htmlBody = $this->htmlBody($code, $purpose);

        if ($apiToken !== '') {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Zoho-enczapikey '.$apiToken,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.zeptomail.com/v1.1/email', [
                    'from' => [
                        'address' => $fromAddress,
                        'name' => $fromName,
                    ],
                    'to' => [
                        [
                            'email_address' => [
                                'address' => $email,
                                'name' => $email,
                            ],
                        ],
                    ],
                    'subject' => $subject,
                    'htmlbody' => $htmlBody,
                ]);

            if ($response->successful()) {
                return;
            }

            $body = $response->json() ?? $response->body();
            $errMsg = is_array($body)
                ? ($body['message'] ?? data_get($body, 'error.message') ?? json_encode($body))
                : (string) $body;

            throw new \RuntimeException('ZeptoMail API error: '.$errMsg);
        }

        Mail::to($email)->send($mailable);
    }

    private function htmlBody(string $code, string $purpose): string
    {
        $title = $purpose === 'reset' ? 'Password reset' : 'Email verification';
        $intro = $purpose === 'reset'
            ? 'Use this code to reset your Scoop password:'
            : 'Use this code to verify your Scoop account:';
        $safeCode = e($code);

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:24px; color:#0f172a;">
  <div style="max-width:480px; margin:0 auto; background:#ffffff; border-radius:12px; padding:28px; border:1px solid #e2e8f0;">
    <h1 style="margin:0 0 8px; font-size:20px;">{$title}</h1>
    <p style="margin:0 0 20px; color:#64748b; font-size:14px;">{$intro}</p>
    <p style="margin:0 0 20px; font-size:32px; letter-spacing:8px; font-weight:700; text-align:center; color:#0A9D9B;">{$safeCode}</p>
    <p style="margin:0; color:#94a3b8; font-size:12px;">This code expires in 15 minutes. If you did not request this, you can ignore this email.</p>
  </div>
</body>
</html>
HTML;
    }
}
