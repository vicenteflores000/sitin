<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'estado_glpi',
    ];

    protected $casts = [
        'updated_at_estado_glpi' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
