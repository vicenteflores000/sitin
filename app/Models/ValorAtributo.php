<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValorAtributo extends Model
{
    protected $table = 'valores_atributo';

    protected $fillable = [
        'activo_id',
        'atributo_id',
        'valor_string',
        'valor_integer',
        'valor_decimal',
        'valor_boolean',
        'valor_text',
        'valor_date',
        'valor_json',
    ];

    protected $casts = [
        'valor_boolean' => 'boolean',
        'valor_date'    => 'date',
        'valor_json'    => 'array',
    ];

    public function activo(): BelongsTo
    {
        return $this->belongsTo(Activo::class);
    }

    public function atributo(): BelongsTo
    {
        return $this->belongsTo(Atributo::class);
    }
}
