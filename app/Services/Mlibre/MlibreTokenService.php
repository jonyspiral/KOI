<?php

namespace App\Services\Mlibre;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MlibreTokenService
{
    public function getValidAccessToken(int $userId = null): string
{
    $userId = $userId ?? env('MLIBRE_USER_ID');
    \Log::info('MLIBRE_USER_ID cargado: ' . ($userId ?? 'null'));

    if (!$userId) {
        throw new \Exception('MLIBRE_USER_ID no está definido en el archivo .env ni se pasó manualmente.');
    }

    $token = DB::table('mlibre_tokens')->where('user_id', $userId)->first();

    if (!$token) {
        throw new \Exception("No hay token guardado para el usuario $userId.");
    }

    if (Carbon::parse($token->expires_at)->isFuture()) {
        return $token->access_token;
    }

    // Renovar token
    $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
        'grant_type' => 'refresh_token',
        'client_id' => env('MLIBRE_APP_ID'),
        'client_secret' => env('MLIBRE_SECRET'),
        'refresh_token' => $token->refresh_token,
    ]);

    if (!$response->ok()) {
        throw new \Exception('Error al renovar token: ' . $response->body());
    }

    $data = $response->json();

    DB::table('mlibre_tokens')->where('user_id', $userId)->update([
        'access_token' => $data['access_token'],
        'refresh_token' => $data['refresh_token'],
        'expires_at' => now()->addSeconds($data['expires_in']),
        'updated_at' => now(),
    ]);

    return $data['access_token'];
}
}
