<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AllowedDomain;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $intended = $this->safeIntendedUrl(url()->previous(), route('home'));
        session(['url.intended' => $intended]);

        return Socialite::driver('microsoft')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();
        } catch (\Throwable $exception) {
            Log::warning('Microsoft OAuth callback failed', [
                'error' => $exception->getMessage(),
            ]);
            return redirect()->route('login')->withErrors([
                'email' => 'No se pudo iniciar sesión con Outlook.',
            ]);
        }

        $email = $microsoftUser->getEmail()
            ?? ($microsoftUser->user['mail'] ?? null)
            ?? ($microsoftUser->user['userPrincipalName'] ?? null);

        if (! $email) {
            Log::warning('Microsoft OAuth callback without email', [
                'payload' => $microsoftUser->user ?? null,
            ]);
            return redirect()->route('login')->withErrors([
                'email' => 'No se pudo obtener el correo desde Outlook.',
            ]);
        }

        if (! AllowedDomain::allowsEmail($email)) {
            Log::warning('Microsoft OAuth blocked by domain policy', [
                'email' => $email,
            ]);
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
                'role' => 'user',
                'active' => true,
            ]);
        }

        if (! $user->isActive()) {
            Log::warning('Microsoft OAuth login for inactive user', [
                'email' => $email,
                'user_id' => $user->id,
            ]);
            return redirect()->route('login')->withErrors([
                'email' => 'Usuario desactivado.',
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('home', absolute: false));
    }

    protected function safeIntendedUrl(?string $url, string $fallback): string
    {
        if (! $url) {
            return $fallback;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if ($host && ! hash_equals($host, request()->getHost())) {
            return $fallback;
        }

        return $url;
    }
}
