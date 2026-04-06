<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Locacion;
use App\Models\User;
use App\Models\TicketStatusEvent;
use App\Models\TicketAssignment;
use App\Models\TicketResolution;
use App\Models\TicketPart;
use App\Models\TicketAction;
use App\Models\TicketSchedule;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'tipo',
        'area',
        'categoria',
        'impacto',
        'descripcion',
        'pc',
        'usuario',
        'usuario_mail',
        'ip_origen',
        'origen',
        'prioridad',
        'urgencia',
        'locacion_id',
        'locacion_hija_texto',
        'categoria_interna',
        'problem_type',
        'root_cause',
        'resolved_by',
        'resolved_at',
        'assisted_by',
        'assisted_channel',
        'assisted_reason',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function locacion(): BelongsTo
    {
        return $this->belongsTo(Locacion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_mail', 'email');
    }

    public function assistedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assisted_by');
    }

    public function getDisplayIdAttribute(): string
    {
        $est = $this->locacion_id ?? 0;
        $ticketId = $this->id ?? 0;
        $userId = $this->requester?->id;

        if (! $userId && $this->usuario_mail) {
            static $emailCache = [];
            $emailKey = strtolower($this->usuario_mail);
            if (array_key_exists($emailKey, $emailCache)) {
                $userId = $emailCache[$emailKey];
            } else {
                $userId = User::where('email', $this->usuario_mail)->value('id');
                $emailCache[$emailKey] = $userId ?: 0;
            }
        }

        $userId = $userId ?? 0;

        return sprintf('%03d%03d%03d', $est, $userId, $ticketId);
    }

    public function statusEvents(): HasMany
    {
        return $this->hasMany(TicketStatusEvent::class);
    }

    public function latestStatusEvent(): HasOne
    {
        return $this->hasOne(TicketStatusEvent::class)->latestOfMany('started_at');
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(TicketAssignment::class)
            ->whereNull('unassigned_at')
            ->latestOfMany('assigned_at');
    }

    public function currentAssignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class)
            ->whereNull('unassigned_at')
            ->orderByDesc('assigned_at');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class);
    }

    public function resolution(): HasOne
    {
        return $this->hasOne(TicketResolution::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(TicketPart::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(TicketAction::class)->orderByDesc('created_at');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TicketSchedule::class)->orderByDesc('start_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->orderByDesc('created_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }
}
