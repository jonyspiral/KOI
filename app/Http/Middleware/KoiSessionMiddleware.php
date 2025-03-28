<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserKoi; // Modelo Eloquent que apunta a la tabla `users`

class KoiSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Solo si no está logueado en Laravel
        if (!Auth::check()) {
            // Leer la cookie PHPSESSID (la que KOI usa)
            $sessionId = $request->cookie('PHPSESSID'); 
            if ($sessionId) {
                // Buscar la fila en koi_sessions
                $row = DB::table('koi_sessions')->where('session_id', $sessionId)->first();
                if ($row) {
                    // Buscar al usuario en la tabla `users`
                    // Ajusta 'cod_usuario' si es la PK
                    $user = UserKoi::find($row->user_id);
                    if ($user) {
                        Auth::login($user);
                    }
                }
            }
        }

        return $next($request);
    }
}
