<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedDomain extends Model
{
    protected $fillable = [
        'domain',
    ];

    public static function allowsEmail(string $email): bool
    {
        $domain = strtolower(trim(substr(strrchr($email, '@'), 1) ?: ''));
        if ($domain === '') {
            return false;
        }

        return static::where('domain', $domain)->exists();
    }
}
