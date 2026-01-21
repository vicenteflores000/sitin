<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\GlpiService;

class LinkGlpiUserOnLogin
{
    public function handle(Login $event)
    {
        try {
            $user = $event->user;

            if ($user->glpi_user_id) {
                return;
            }

            $glpi = app(GlpiService::class);
            $glpiUserId = $glpi->getUserIdByEmail($user->email);

            if ($glpiUserId) {
                $user->update([
                    'glpi_user_id' => $glpiUserId
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('No se pudo vincular usuario con GLPI', [
                'email' => $event->user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
