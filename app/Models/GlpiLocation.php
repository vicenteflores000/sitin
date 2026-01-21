<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlpiLocation extends Model
{
    protected $fillable = [
        'glpi_id',
        'name',
        'parent_id',
    ];
}
