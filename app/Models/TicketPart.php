<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPart extends Model
{
    protected $fillable = [
        'ticket_id',
        'part_name',
        'quantity',
        'status',
        'noted_at',
    ];

    protected $casts = [
        'noted_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
