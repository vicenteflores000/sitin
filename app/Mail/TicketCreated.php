<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket)
    {
    }

    public function build()
    {
        return $this->subject("Nuevo ticket #{$this->ticket->id}")
            ->view('emails.tickets.created', [
                'logoUrl' => asset('images/logo.png'),
            ]);
    }
}
