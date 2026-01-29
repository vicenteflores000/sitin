<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoActivo extends Model
{
    protected $table = 'tipos_activo';

    protected $fillable = [
        'nombre',
        'slug',
    ];

    /** Un tipo tiene muchos activos */
    public function activos(): HasMany
    {
        return $this->hasMany(Activo::class);
    }

    /** Un tipo tiene muchos atributos */
    public function atributos(): BelongsToMany
    {
        return $this->belongsToMany(
            Atributo::class,
            'atributo_tipo_activo'
        );
    }
}
