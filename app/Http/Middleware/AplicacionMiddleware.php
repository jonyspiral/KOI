<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AplicacionMiddleware
{
    public function handle($request, Closure $next, $app)
    {
        $user = Auth::user();

        if (!$user || $user->aplicacion_default !== $app) {
            abort(403, 'Acceso denegado a esta aplicación');
        }

        return $next($request);
    }
}
