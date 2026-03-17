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
        'estado_envio_glpi',
        'prioridad',
        'urgencia',
        'glpi_ticket_id',
        'payload_glpi',
        'glpi_location_id',
        'locacion_id',
        'locacion_hija_texto',
        'estado_glpi',
        'categoria_interna',
        'problem_type',
        'root_cause',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'updated_at_estado_glpi' => 'datetime',
    ];

    public function locacion(): BelongsTo
    {
        return $this->belongsTo(Locacion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
