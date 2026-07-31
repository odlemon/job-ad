<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $passwordPlain;

    public function __construct(string $name, string $email, string $passwordPlain)
    {
        $this->name = $name;
        $this->email = $email;
        $this->passwordPlain = $passwordPlain;
    }

    public function build(): self
    {
        return $this
            ->from('noreply@kyntaro.com', 'Chommie')
            ->subject('Your Admin Dashboard Access')
            ->view('emails.admin-credentials');
    }
}

