<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activo extends Model
{
    protected $fillable = [
        'tipo_activo_id',
        'nombre',
        'codigo_interno',
        'estado',
        'observaciones',
    ];

    /** Activo pertenece a un tipo */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoActivo::class, 'tipo_activo_id');
    }

    /** Valores dinámicos */
    public function valoresAtributo(): HasMany
    {
        return $this->hasMany(ValorAtributo::class);
    }

    /** Asignaciones (usuario o locación) */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionActivo::class);
    }

    /** Asignación activa */
    public function asignacionActiva()
    {
        return $this->asignaciones()->whereNull('hasta')->latest('desde');
    }
}
