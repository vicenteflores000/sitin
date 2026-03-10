<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\User;

class Locacion extends Model
{
    protected $table = 'locaciones';

    protected $fillable = [
        'nombre',
        'slug',
        'locacion_padre_id',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /** Locación padre */
    public function padre(): BelongsTo
    {
        return $this->belongsTo(Locacion::class, 'locacion_padre_id');
    }

    /** Locaciones hijos */
    public function hijos(): HasMany
    {
        return $this->hasMany(Locacion::class, 'locacion_padre_id');
    }

    /** Funcionarios vinculados */
    public function funcionarios(): HasMany
    {
        return $this->hasMany(User::class, 'locacion_id');
    }

    /** Activos asignados */
    public function activosAsignados(): MorphMany
    {
        return $this->morphMany(AsignacionActivo::class, 'asignable');
    }

    public function scopeRaiz(Builder $query)
    {
        return $query->whereNull('locacion_padre_id');
    }
}
