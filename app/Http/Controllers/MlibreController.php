<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MlibreToken;
use Carbon\Carbon;


class MlibreController extends Controller
{
    public function callback(Request $request)
{
    $code = $request->input('code');

    if (!$code) {
        return response()->json(['status' => 'error', 'message' => 'Code no recibido']);
    }

    $clientId = '3974289321121032';
    $clientSecret = 'hNha5KwSz3k1qtlpXnGoz3CINJNyZ42X';
    $redirectUri = 'https://devkoi2.spiralshoes.com/meli/callback';

    $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'code' => $code,
        'redirect_uri' => $redirectUri,
    ]);

    if (!$response->successful()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No se pudo obtener el token',
            'response' => $response->body()
        ], $response->status());
    }

    $data = $response->json();

    // Guardar o actualizar en base de datos
    MlibreToken::updateOrCreate(
        ['user_id' => $data['user_id']],
        [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at' => Carbon::now()->addSeconds($data['expires_in']),
        ]
    );

    return response()->json([
        'status' => 'success',
        'user_id' => $data['user_id'],
        'access_token' => $data['access_token'],
        'expires_at' => Carbon::now()->addSeconds($data['expires_in'])->toDateTimeString()
    ]);
}
private function getValidAccessToken(): ?string
{
    $token = MlibreToken::first(); // o ->where('user_id', ...)->first() si tenés varios

    if (!$token) {
        return null;
    }

    if (Carbon::now()->lt($token->expires_at)) {
        return $token->access_token; // aún válido
    }

    // Token vencido: refrescar
    $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
        'grant_type' => 'refresh_token',
        'client_id' => '3974289321121032',
        'client_secret' => 'hNha5KwSz3k1qtlpXnGoz3CINJNyZ42X',
        'refresh_token' => $token->refresh_token,
    ]);

    if (!$response->successful()) {
        \Log::error('❌ Error al refrescar el token ML', ['response' => $response->body()]);
        return null;
    }

    $data = $response->json();

    $token->update([
        'access_token' => $data['access_token'],
        'refresh_token' => $data['refresh_token'],
        'expires_at' => Carbon::now()->addSeconds($data['expires_in']),
    ]);

    return $data['access_token'];
}


public function publicarTest()
{
   $accessToken = 'APP_USR-3974289321121032-060515-7d916576e9092be3339564132254e4f4-2481996336';


    $payload = [
        "title" => "Zapatilla de prueba KOI2",
        "category_id" => "MLA3530",
        "price" => 1000,
        "currency_id" => "ARS",
        "available_quantity" => 1,
        "buying_mode" => "buy_it_now",
        "condition" => "new",
        "listing_type_id" => "gold_special",

        "description" => [
            "plain_text" => "Zapatilla de prueba para la integración entre KOI2 y Mercado Libre"
        ],
        "pictures" => [
            [
                "source" => "https://http2.mlstatic.com/storage/sellers-guide/mla-index/camera.jpg"
            ]
        ],
        "attributes" => [
            [
                "id" => "BRAND",
                "value_name" => "SPIRAL"
            ],
            [
                "id" => "MODEL",
                "value_name" => "DROP"
            ],
            [
                "id" => "EMPTY_GTIN_REASON",
                "value_id" => "17055160" // El producto no tiene código registrado
            ],
            [
                "id" => "VALUE_ADDED_TAX",
                "value_id" => "48405909" // 21 %
            ],
            [
                "id" => "IMPORT_DUTY",
                "value_id" => "49553241" // 2.5 %
            ]
        ]
    ];

    $response = Http::withToken($accessToken)
        ->post('https://api.mercadolibre.com/items', $payload);

    dd($response->json());
}

public function testCategoria()
{
    $token = MlibreToken::first()?->access_token;

    if (!$token) {
        return response()->json(['status' => 'error', 'message' => 'Token no encontrado']);
    }

    $response = Http::withToken($token)
        ->get("https://api.mercadolibre.com/categories/MLA3530/attributes");

    return response()->json([
        'status' => $response->successful() ? 'success' : 'error',
        'response' => $response->json(),
    ]);
}
}