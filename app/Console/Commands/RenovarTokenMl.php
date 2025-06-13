<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class RenovarTokenMl extends Command
{
    protected $signature = 'mlibre:renovar-tokens';

    protected $description = 'Renueva el token de acceso de Mercado Libre manualmente';

    public function handle()
    {
        $token = DB::table('mlibre_tokens')->first();

        if (!$token) {
            $this->error('No se encontró un token previo en la base de datos.');
            return;
        }

        $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
            'grant_type'    => 'refresh_token',
            'client_id'     => env('MLIBRE_APP_ID'),
            'client_secret' => env('MLIBRE_SECRET'),
            'refresh_token' => $token->refresh_token,
        ]);

        if (!$response->ok()) {
            $this->error('Error al renovar token: ' . $response->body());
            return;
        }

        $data = $response->json();

        DB::table('mlibre_tokens')->update([
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at'    => Carbon::now()->addSeconds($data['expires_in']),
            'updated_at'    => now(),
        ]);

        if (!$this->output->isQuiet()) {
            $this->info('✅ Token renovado exitosamente.');
        }
    }
}
