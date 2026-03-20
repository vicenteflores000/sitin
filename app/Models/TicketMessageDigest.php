<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessageDigest extends Model
{
    protected $fillable = [
        'ticket_id',
        'recipient_email',
        'pending_count',
        'last_sent_message_id',
        'send_after',
    ];

    protected $casts = [
        'send_after' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
