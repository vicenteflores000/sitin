<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public User $technician
    ) {
    }

    public function build()
    {
        $subject = "Ticket Asignado a {$this->technician->name}";

        return $this->subject($subject)
            ->view('emails.tickets.assigned', [
                'logoUrl' => asset('images/logo.png'),
                'technician' => $this->technician,
            ]);
    }
}
