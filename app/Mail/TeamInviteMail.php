<?php

namespace App\Mail;

use App\Models\CompanyTeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyTeamMember $member,
        public string $companyName,
        public string $inviterName
    ) {
    }

    public function build(): self
    {
        $acceptUrl = url('/team/invite/' . $this->member->invite_token);

        return $this
            ->from(config('mail.from.address', 'noreply@kyntaro.com'), config('mail.from.name', 'JobHub'))
            ->subject("You're invited to join {$this->companyName} on JobHub")
            ->view('emails.team-invite', [
                'memberName' => $this->member->name,
                'companyName' => $this->companyName,
                'inviterName' => $this->inviterName,
                'role' => $this->member->role,
                'acceptUrl' => $acceptUrl,
            ]);
    }
}
