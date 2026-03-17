<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketActionLogged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public TicketAction $action,
        public User $technician
    ) {
    }

    public function build()
    {
        $subject = "Nueva acción en Ticket #{$this->ticket->id}";

        return $this->subject($subject)
            ->view('emails.tickets.action', [
                'logoUrl' => asset('images/logo.png'),
                'technician' => $this->technician,
                'action' => $this->action,
            ]);
    }
}
