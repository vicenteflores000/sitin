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
        $this->ticket->loadMissing('currentAssignments.technician');
        $assignedNames = $this->ticket->currentAssignments
            ->pluck('technician.name')
            ->filter()
            ->unique()
            ->values()
            ->join(', ');

        $subject = "Ticket #{$this->ticket->display_id} asignado al equipo técnico";

        return $this->subject($subject)
            ->view('emails.tickets.assigned', [
                'logoUrl' => asset('images/logo.png'),
                'assignedNames' => $assignedNames,
            ]);
    }
}
