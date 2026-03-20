<?php

namespace App\Console\Commands;

use App\Mail\TicketMessageDigestMail;
use App\Models\TicketMessageDigest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTicketMessageDigests extends Command
{
    protected $signature = 'tickets:send-message-digests';
    protected $description = 'Envía correos agrupados por nuevos mensajes de tickets.';

    public function handle(): int
    {
        $digests = TicketMessageDigest::with('ticket')
            ->whereNotNull('send_after')
            ->where('send_after', '<=', now())
            ->where('pending_count', '>', 0)
            ->get();

        foreach ($digests as $digest) {
            $ticket = $digest->ticket;
            if (! $ticket) {
                $digest->delete();
                continue;
            }

            $messagesQuery = $ticket->messages()->with('user');
            if ($digest->last_sent_message_id) {
                $messagesQuery->where('id', '>', $digest->last_sent_message_id);
            }

            $messages = $messagesQuery->orderBy('id')->get();
            if ($messages->isEmpty()) {
                $digest->pending_count = 0;
                $digest->send_after = null;
                $digest->save();
                continue;
            }

            $recipientEmail = $digest->recipient_email;
            $isRequester = strcasecmp((string) $ticket->usuario_mail, (string) $recipientEmail) === 0;
            $link = $isRequester
                ? route('dashboard', ['ticket' => $ticket->id, 'tab' => 'chat'])
                : route('admin.dashboard', ['ticket' => $ticket->id, 'tab' => 'chat']);

            try {
                Mail::to($recipientEmail)->send(new TicketMessageDigestMail($ticket, $messages, $link));
            } catch (\Throwable $exception) {
                Log::warning('No se pudo enviar digest de chat', [
                    'ticket_id' => $ticket->id,
                    'recipient' => $recipientEmail,
                    'error' => $exception->getMessage(),
                ]);
                $digest->send_after = now()->addSeconds(60);
                $digest->save();
                continue;
            }

            $digest->last_sent_message_id = $messages->last()->id;
            $digest->pending_count = 0;
            $digest->send_after = null;
            $digest->save();
        }

        return Command::SUCCESS;
    }
}
