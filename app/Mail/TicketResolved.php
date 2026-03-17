<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketResolution;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketResolved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public TicketResolution $resolution,
        public User $technician
    ) {
    }

    public function build()
    {
        $subject = "Ticket #{$this->ticket->id} resuelto";

        return $this->subject($subject)
            ->view('emails.tickets.resolved', [
                'logoUrl' => asset('images/logo.png'),
                'technician' => $this->technician,
                'resolution' => $this->resolution,
            ]);
    }
}
