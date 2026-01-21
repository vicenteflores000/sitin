<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->user()?->isActive()) {
            auth()->logout();
            abort(403, 'Usuario desactivado');
        }

        return $next($request);
    }
}
