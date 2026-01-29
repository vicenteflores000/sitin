<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AsignacionActivo extends Model
{
    protected $table = 'asignacion_activo';

    protected $fillable = [
        'activo_id',
        'tipo_asignacion',
        'asignable_id',
        'fecha_asignacion',
        'fecha_desasignacion',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_desasignacion' => 'datetime',
    ];

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class);
    }

    /** Usuario o Locación */
    public function asignable(): MorphTo
    {
        return $this->morphTo();
    }
}
