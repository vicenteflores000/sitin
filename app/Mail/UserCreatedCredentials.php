<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreatedCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword
    ) {
    }

    public function build()
    {
        return $this->subject('Tu acceso a Tickets TI')
            ->view('emails.auth.user-created', [
                'logoUrl' => asset('images/logo.png'),
            ]);
    }
}
