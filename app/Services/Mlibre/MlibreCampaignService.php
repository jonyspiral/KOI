<?php
namespace App\Services\Mlibre;

use Illuminate\Support\Facades\Http;

class MlibreCampaignService
{
   public static function detectCampaignsForItem(string $itemId, string $accessToken, string $userId): array
{
    try {
        $results = [];

        $response = Http::withToken($accessToken)
            ->get("https://api.mercadolibre.com/seller-promotions/users/{$userId}?app_version=v2");

        if (!$response->ok()) {
            return [
                'item_id' => $itemId,
                'campaigns' => [],
                'has_any' => false,
                'error' => 'Error retrieving promotions: ' . $response->status()
            ];
        }

        $promotions = $response->json()['results'] ?? [];

        foreach ($promotions as $promo) {
            if (($promo['type'] ?? '') === 'DEAL') {
                $campaignId = $promo['id'];
                $itemsResp = Http::withToken($accessToken)
                    ->get("https://api.mercadolibre.com/seller-promotions/promotions/{$campaignId}/items?promotion_type=DEAL&app_version=v2&item_id={$itemId}");

                if (!empty($itemsResp->json()['results'])) {
                    $results[] = [
                        'id' => $campaignId,
                        'name' => $promo['name'] ?? '',
                        'status' => $promo['status'] ?? '',
                        'start_date' => $promo['start_date'] ?? null,
                        'finish_date' => $promo['finish_date'] ?? null,
                    ];
                }
            }
        }

        return [
            'item_id' => $itemId,
            'campaigns' => $results,
            'has_any' => count($results) > 0,
            'error' => null,
        ];
    } catch (\Throwable $e) {
        return [
            'item_id' => $itemId,
            'campaigns' => [],
            'has_any' => false,
            'error' => $e->getMessage(),
        ];
    }
}



public static function syncCampaignItems(): void
{
    $userId = env('MLIBRE_USER_ID');
    $token = app(\App\Services\Mlibre\MlibreTokenService::class)->getValidAccessToken($userId);

    $response = Http::ml($token)->get("https://api.mercadolibre.com/seller-promotions/users/{$userId}?app_version=v2");
    $promotions = $response->json()['results'] ?? [];

    foreach ($promotions as $promo) {
        if (($promo['type'] ?? '') !== 'DEAL') {
            continue;
        }

        $campaign = \App\Models\MlCampaign::updateOrCreate(
            ['ml_campaign_id' => $promo['id']],
            [
                'name'       => $promo['name'] ?? '',
                'type'       => $promo['type'] ?? '',
                'status'     => $promo['status'] ?? '',
                'start_date' => $promo['start_date'] ?? null,
                'end_date'   => $promo['finish_date'] ?? null,
            ]
        );

        $itemsResponse = Http::ml($token)->get("https://api.mercadolibre.com/seller-promotions/promotions/{$promo['id']}/items?promotion_type=DEAL&app_version=v2");
        $items = $itemsResponse->json()['results'] ?? [];

        foreach ($items as $item) {
            $itemId = $item['item_id'] ?? null;
            if (!$itemId) continue;

            $variante = \App\Models\MlVariante::where('ml_id', $itemId)->first();

            \App\Models\MlCampaignItem::updateOrCreate(
                [
                    'item_id'        => $itemId,
                    'ml_campaign_id' => $campaign->id, // 🟢 Aquí va el ID numérico, no el objeto
                ],
                [
                    'ml_variantes_id' => $variante?->id,
                ]
            );
        }
    }
}


}
