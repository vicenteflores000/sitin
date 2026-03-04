<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AllowedDomain;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        session(['url.intended' => url()->previous() ?: route('home')]);

        return Socialite::driver('microsoft')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();
        } catch (\Throwable $exception) {
            return redirect()->route('login')->withErrors([
                'email' => 'No se pudo iniciar sesión con Outlook.',
            ]);
        }

        $email = $microsoftUser->getEmail()
            ?? ($microsoftUser->user['mail'] ?? null)
            ?? ($microsoftUser->user['userPrincipalName'] ?? null);

        if (! $email) {
            return redirect()->route('login')->withErrors([
                'email' => 'No se pudo obtener el correo desde Outlook.',
            ]);
        }

        if (! AllowedDomain::allowsEmail($email)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Dominio no permitido.',
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $name = $microsoftUser->getName()
                ?? $microsoftUser->getNickname()
                ?? Str::before($email, '@');

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Str::random(32),
                'must_change_password' => false,
                'glpi_user_id' => null,
                'role' => 'user',
                'active' => true,
            ]);
        }

        if (! $user->isActive()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Usuario desactivado.',
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('home', absolute: false));
    }
}
