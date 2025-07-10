<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MlOrden;
use App\Models\MlOrdenItem;
use Carbon\Carbon;

class MlibreSincronizarOrdenes extends Command
{
    protected $signature = 'mlibre:sincronizar-ordenes';
    protected $description = 'Sincroniza órdenes pagadas desde la API de Mercado Libre';

    public function handle()
    {
        $this->info('🔄 Iniciando sincronización de órdenes ML...');

        $userId = env('MLIBRE_USER_ID');
        $accessToken = app(\App\Services\Mlibre\MlibreTokenService::class)->getValidAccessToken($userId);

        $from = Carbon::now()->subMonths(6)->startOfMonth()->toIso8601String();
        $to   = Carbon::now()->endOfMonth()->toIso8601String();

        $offset = 0;
        $limit = 50;

        do {
            $url = 'https://api.mercadolibre.com/orders/search';
            $params = [
                'seller' => $userId,
                'order.status' => 'paid',
                'date_created.from' => $from,
                'date_created.to' => $to,
                'limit' => $limit,
                'offset' => $offset,
                'sort' => 'date_desc',
                'access_token' => $accessToken,
            ];

            $response = Http::get($url, $params);
            $data = $response->json();

            foreach ($data['results'] ?? [] as $orden) {
                $this->importarOrden($orden);
            }

            $offset += $limit;
        } while (!empty($data['results']));

        $this->info('✅ Sincronización completa.');
    }

    protected function importarOrden(array $orden)
{
    $registro = MlOrden::updateOrCreate(
        ['id' => $orden['id']],
        [
            'date_created'       => $orden['date_created'],
            'date_closed'        => $orden['date_closed'] ?? null,
            'status'             => $orden['status'],
            'status_detail'      => $orden['status_detail'] ?? null,
            'fulfilled'          => $orden['fulfilled'] ?? false,
            'total_amount'       => $orden['total_amount'],
            'paid_amount'        => $orden['paid_amount'],
            'coupon_amount'      => $orden['coupon']['amount'] ?? 0,
            'shipping_cost'      => $orden['shipping']['cost'] ?? 0,
            'transaction_amount' => collect($orden['order_items'] ?? [])
                                        ->sum(fn($i) => $i['unit_price'] * $i['quantity']),
            'shipping_id'        => $orden['shipping']['id'] ?? null,
            'shipping_status'    => $orden['shipping']['status'] ?? null,
            'shipping_substatus' => $orden['shipping']['substatus'] ?? null,
            'shipping_mode'      => $orden['shipping']['shipping_mode'] ?? null,
            'logistics_type'     => $orden['shipping']['logistics_type'] ?? null,
            'receiver_city'      => $orden['shipping']['receiver_address']['city']['name'] ?? null,
            'receiver_state'     => $orden['shipping']['receiver_address']['state']['name'] ?? null,
            'receiver_zip'       => $orden['shipping']['receiver_address']['zip_code'] ?? null,
            'buyer_id'           => $orden['buyer']['id'],
            'buyer_nickname'     => $orden['buyer']['nickname'],
            'buyer_email'        => $orden['buyer']['email'] ?? null,
            'buyer_first_name'   => $orden['buyer']['first_name'] ?? null,
            'buyer_last_name'    => $orden['buyer']['last_name'] ?? null,
            'buyer_doc_type'     => $orden['buyer']['billing_info']['doc_type'] ?? null,
            'buyer_doc_number'   => $orden['buyer']['billing_info']['doc_number'] ?? null,
            'payment_method'     => $orden['payments'][0]['payment_method_id'] ?? null,
            'payment_type'       => $orden['payments'][0]['payment_type'] ?? null,
            'installments'       => $orden['payments'][0]['installments'] ?? null,
            'date_approved'      => $orden['payments'][0]['date_approved'] ?? null,
            'tags'               => $orden['tags'] ?? [],
        ]
    );

    $registro->items()->delete();

    foreach ($orden['order_items'] as $item) {
        $registro->items()->create([
            'ml_id'               => $item['item']['id'],
            'variation_id'        => $item['item']['variation_id'],
            'seller_custom_field' => $item['item']['seller_custom_field'] ?? null,
            'title'               => $item['item']['title'],
            'quantity'            => $item['quantity'],
            'unit_price'          => $item['unit_price'],
            'full_unit_price'     => $item['full_unit_price'] ?? $item['unit_price'],
            'currency'            => $item['currency_id'] ?? 'ARS',
            'category_id'         => $item['item']['category_id'] ?? null,
            'permalink'           => $item['item']['permalink'] ?? null,
            'attributes'          => json_encode($item['item']['attributes'] ?? []),
        ]);
    }

    $this->line(sprintf('📝 Orden sincronizada: %s (%s)', $orden['id'], $registro->status));
}

}
