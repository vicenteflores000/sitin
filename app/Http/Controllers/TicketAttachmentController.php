<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TicketAttachmentController extends Controller
{
    public function show(TicketAttachment $attachment)
    {
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        $ticket = $attachment->ticket;
        if (! $ticket) {
            abort(404);
        }

        $canAccess = $user->isAdmin() || strcasecmp((string) $ticket->usuario_mail, (string) $user->email) === 0;
        if (! $canAccess) {
            abort(403);
        }

        $path = $attachment->path;
        $disk = Storage::disk('local')->exists($path) ? 'local' : 'public';
        if (! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        $fullPath = Storage::disk($disk)->path($path);
        $mime = $attachment->mime_type ?: 'application/octet-stream';
        $filename = $attachment->original_name ?: basename($path);
        $filename = str_replace(["\r", "\n"], '', $filename);

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
        ]);
    }
}
