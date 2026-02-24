<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->must_change_password) {
            return $next($request);
        }

        if (
            $request->routeIs('password.force.show') ||
            $request->routeIs('password.force.update') ||
            $request->routeIs('logout')
        ) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Debes cambiar tu clave provisoria antes de continuar.',
                'redirect' => route('password.force.show'),
            ], 423);
        }

        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('password.force.show');
    }
}
