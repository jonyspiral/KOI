<?php

namespace App\Services\Mlibre;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MlibreTokenService
{
    /**
     * Devuelve un access_token válido.
     * Si está vencido, lo renueva automáticamente usando el refresh_token.
     */
    public function getValidAccessToken(): string
    {
        // 🔒 Por ahora solo manejamos una cuenta ML
        $token = DB::table('mlibre_tokens')->first();

        if (!$token) {
            throw new \Exception('No hay token almacenado en la tabla mlibre_tokens.');
        }

        // ✅ Si el token sigue vigente, lo usamos
        if (Carbon::parse($token->expires_at)->isFuture()) {
            return $token->access_token;
        }

        // 🔄 Token vencido → renovamos usando refresh_token
        $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
            'grant_type'    => 'refresh_token',
            'client_id'     => env('MLIBRE_APP_ID'),
            'client_secret' => env('MLIBRE_SECRET'),
            'refresh_token' => $token->refresh_token,
        ]);

        if (!$response->ok()) {
            throw new \Exception('Error al renovar token Mercado Libre: ' . $response->body());
        }

        $data = $response->json();

        // 📝 Actualizar en base de datos
        DB::table('mlibre_tokens')->update([
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at'    => now()->addSeconds($data['expires_in']),
            'updated_at'    => now(),
        ]);

        return $data['access_token'];
    }
}
