<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public TicketSchedule $schedule,
        public string $mode = 'created'
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

        $subject = match ($this->mode) {
            'updated' => "Ticket #{$this->ticket->display_id} reprogramado",
            'deleted' => "Ticket #{$this->ticket->display_id} cancelado",
            default => "Ticket #{$this->ticket->display_id} agendado",
        };

        return $this->subject($subject)
            ->view('emails.tickets.scheduled', [
                'logoUrl' => asset('images/logo.png'),
                'mode' => $this->mode,
                'assignedNames' => $assignedNames,
            ]);
    }
}
