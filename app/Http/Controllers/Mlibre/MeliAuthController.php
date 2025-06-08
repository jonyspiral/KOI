<?php

namespace App\Http\Controllers\Mlibre;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MlibreToken;
use Carbon\Carbon;


class MeliAuthController extends Controller
{
    public function callback(Request $request)
    {
        $code = $request->input('code');

        if (!$code) {
            return response()->json(['status' => 'error', 'message' => 'Code no recibido']);
        }

        $clientId = env('MLIBRE_APP_ID');
        $clientSecret = env('MLIBRE_SECRET');
        $redirectUri = env('MLIBRE_REDIRECT_URI');

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
        $clientId = env('MLIBRE_APP_ID');
        $clientSecret = env('MLIBRE_SECRET');

        $token = MlibreToken::first();

        if (!$token) {
            return null;
        }

        if (Carbon::now()->lt($token->expires_at)) {
            return $token->access_token;
        }

        $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
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

    public function redirect()
    {
        $clientId = env('MLIBRE_APP_ID');
        $redirectUri = env('MLIBRE_REDIRECT_URI');

        $url = 'https://auth.mercadolibre.com.ar/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
        ]);

        return redirect($url);
    }

    public function publicarTest()
    {
        $accessToken = $this->getValidAccessToken();

        if (!$accessToken) {
            return response()->json(['error' => 'No se pudo obtener token válido']);
        }

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
                ["source" => "https://http2.mlstatic.com/storage/sellers-guide/mla-index/camera.jpg"]
            ],
            "attributes" => [
                ["id" => "BRAND", "value_name" => "SPIRAL"],
                ["id" => "MODEL", "value_name" => "DROP"],
                ["id" => "EMPTY_GTIN_REASON", "value_id" => "17055160"],
                ["id" => "VALUE_ADDED_TAX", "value_id" => "48405909"],
                ["id" => "IMPORT_DUTY", "value_id" => "49553241"]
            ]
        ];

        $response = Http::withToken($accessToken)
            ->post('https://api.mercadolibre.com/items', $payload);

        return response()->json($response->json());
    }

    public function testCategoria()
    {
        $accessToken = $this->getValidAccessToken();

        if (!$accessToken) {
            return response()->json(['status' => 'error', 'message' => 'Token no válido']);
        }

        $response = Http::withToken($accessToken)
            ->get("https://api.mercadolibre.com/categories/MLA3530/attributes");

        return response()->json([
            'status' => $response->successful() ? 'success' : 'error',
            'response' => $response->json(),
        ]);
    }
}
