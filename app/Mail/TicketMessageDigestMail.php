<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Models\Ticket;

class TicketMessageDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public Collection $messages;
    public string $link;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, Collection $messages, string $link)
    {
        $this->ticket = $ticket;
        $this->messages = $messages;
        $this->link = $link;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $count = $this->messages->count();
        return new Envelope(
            subject: "#{$this->ticket->display_id} | Tienes {$count} mensajes nuevos",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-message-digest',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
