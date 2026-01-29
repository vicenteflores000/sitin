<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atributo extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'tipo_dato',
        'unidad',
        'es_requerido',
    ];

    /** Tipos de activo donde aplica */
    public function tiposActivo(): BelongsToMany
    {
        return $this->belongsToMany(
            TipoActivo::class,
            'atributo_tipo_activo'
        );
    }

    /** Valores asignados */
    public function valores(): HasMany
    {
        return $this->hasMany(ValorAtributo::class);
    }
}
