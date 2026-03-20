<?php

namespace App\Http\Controllers;

use App\Mail\TicketMessageDigestMail;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketMessageDigest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketMessageController extends Controller
{
    public function index(Ticket $ticket): JsonResponse
    {
        $user = auth()->user();
        if (! $user || ! $this->canAccess($ticket, $user)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $messages = $ticket->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get()
            ->map(function (TicketMessage $message) use ($user) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'created_at' => $message->created_at?->format('d-m-Y H:i') ?? '',
                    'user_name' => $message->user?->name ?? 'Usuario',
                    'user_email' => $message->user?->email ?? '',
                    'is_own' => $message->user_id === $user->id,
                ];
            });

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, Ticket $ticket): JsonResponse
    {
        $user = auth()->user();
        if (! $user || ! $this->canAccess($ticket, $user)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $messageText = trim($data['message']);
        if ($messageText === '') {
            return response()->json(['message' => 'Mensaje vacío.'], 422);
        }

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $messageText,
        ]);

        $this->queueDigestNotifications($ticket, $user);

        return response()->json([
            'status' => 'ok',
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'created_at' => $message->created_at?->format('d-m-Y H:i') ?? '',
                'user_name' => $user->name,
                'user_email' => $user->email,
                'is_own' => true,
            ],
        ], 201);
    }

    protected function canAccess(Ticket $ticket, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (strcasecmp((string) $ticket->usuario_mail, (string) $user->email) === 0) {
            return true;
        }

        return $ticket->currentAssignments()
            ->where('technician_id', $user->id)
            ->exists();
    }

    protected function queueDigestNotifications(Ticket $ticket, User $sender): void
    {
        $ticket->loadMissing('currentAssignments.technician');

        $recipients = collect($ticket->currentAssignments)
            ->pluck('technician.email')
            ->filter();

        if ($ticket->usuario_mail) {
            $recipients->push($ticket->usuario_mail);
        }

        $recipients = $recipients
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter()
            ->unique()
            ->reject(fn ($email) => strtolower((string) $sender->email) === $email)
            ->values();

        $recipients = $recipients
            ->push('informatica@mdonihue.cl')
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter()
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        $delaySeconds = (int) config('tickets.chat_digest_delay', 60);
        $sendAfter = now()->addSeconds(max($delaySeconds, 10));

        foreach ($recipients as $recipient) {
            $digest = TicketMessageDigest::firstOrNew([
                'ticket_id' => $ticket->id,
                'recipient_email' => $recipient,
            ]);

            $digest->pending_count = ($digest->pending_count ?? 0) + 1;
            $digest->send_after = $sendAfter;
            $digest->save();
        }
    }
}
