<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\Mlibre\MlibreTokenService;

class MlibreImportarOrdenes extends Command
{
    protected $signature = 'mlibre:importar-ordenes {desde} {hasta}';
    protected $description = 'Importa órdenes de Mercado Libre entre fechas (yyyy-mm-dd) para facturación manual';

    public function handle()
    {
        $desde = $this->argument('desde');
        $hasta = $this->argument('hasta');

        // 🔐 Cargar el user_id desde .env o directamente usar 448490530
        $userId = env('MLIBRE_USER_ID', 448490530);

        // ✅ Obtener el access_token válido para el user_id correcto
        $token = app(MlibreTokenService::class)->getValidAccessToken($userId);

        $this->info("📦 Importando órdenes desde $desde hasta $hasta para user_id $userId...");

        $offset = 0;
        $limit = 50;
        $ordenes = [];

        do {
            $response = Http::withHeaders([
    'Authorization' => "Bearer $token",
    'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
    'Accept'        => 'application/json',
    'Content-Type'  => 'application/json',
])->get('https://api.mercadolibre.com/orders/search', [
    'seller' => 448490530,
    'order.status' => 'paid',
    'order.date_created.from' => "{$desde}T00:00:00.000-03:00",
    'order.date_created.to'   => "{$hasta}T23:59:59.000-03:00",
    'sort' => 'date_desc',
    'limit' => 50,
    'offset' => $offset,
]);

            if ($response->failed()) {
                $this->error('❌ Error al consultar órdenes: ' . $response->body());
                return 1;
            }

            $result = $response->json();
            $results = $result['results'] ?? [];
            if (empty($results)) break;

            foreach ($results as $order) {
                $detalle = $this->getOrderDetail($order['id'], $token);
                if ($detalle) $ordenes[] = $detalle;
            }

            $offset += $limit;
        } while (count($results) === $limit);

        // 💾 Guardar CSV
        $csv = $this->convertToCsv($ordenes);
        $filename = "ordenes_ml_{$desde}_{$hasta}.csv";
        Storage::disk('local')->put($filename, $csv);

        $this->info("✅ CSV generado: storage/app/$filename");
        return 0;
    }

    private function getOrderDetail($orderId, $token)
{
    $res = Http::withHeaders([
        'Authorization' => "Bearer $token",
        'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
    ])->get("https://api.mercadolibre.com/orders/{$orderId}");

    if ($res->failed()) {
        $this->error("❌ Falló al obtener orden $orderId: ".$res->body());
        return null;
    }

    $o = $res->json();

    // Items: usa item.title si existe; agrega variaciones si están disponibles
    $items = collect($o['order_items'] ?? [])->map(function ($i) {
        $title = $i['item']['title'] ?? ($i['title'] ?? ($i['item']['id'] ?? ''));
        $attrs = collect($i['item']['variation_attributes'] ?? $i['variation_attributes'] ?? [])
            ->map(fn ($a) => ($a['name'] ?? '') . ': ' . ($a['value_name'] ?? ''))
            ->filter()->implode(', ');
        return trim($title . ($attrs ? " ($attrs)" : ''));
    })->filter()->implode(' | ');

    // Documento del comprador: intenta billing_info, si no, identification
    $doc = $o['buyer']['billing_info']['doc_number']
        ?? $o['buyer']['identification']['number']
        ?? '';

    // Monto: intenta total_amount; si no, paid_amount; si no, suma de pagos
    $monto = $o['total_amount']
        ?? $o['paid_amount']
        ?? collect($o['payments'] ?? [])->sum(fn ($p) => $p['total_paid_amount'] ?? $p['transaction_amount'] ?? 0);

    // Medio de pago (primer pago si existe)
    $medioPago = '';
    if (!empty($o['payments']) && is_array($o['payments'])) {
        $p0 = $o['payments'][0];
        $medioPago = $p0['payment_type'] ?? $p0['payment_type_id'] ?? $p0['method'] ?? ($p0['payment_method_id'] ?? '');
    }

    // Dirección: puede venir vacía en la orden → si hay shipping.id, consultamos /shipments/{id}
    $direccion = $o['shipping']['receiver_address']['address_line']
        ?? $o['shipping']['receiver_address']['address']
        ?? '';

    if ($direccion === '' && !empty($o['shipping']['id'])) {
        $ship = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])->get("https://api.mercadolibre.com/shipments/{$o['shipping']['id']}");
        if ($ship->ok()) {
            $sj = $ship->json();
            $direccion = $sj['receiver_address']['address_line']
                ?? ($sj['receiver_address']['street_name'] ?? '') . ' ' . ($sj['receiver_address']['street_number'] ?? '');
            $direccion = trim($direccion);
        }
    }

    return [
        'id'         => (string)($o['id'] ?? ''),
        'fecha'      => (string)($o['date_created'] ?? ''),
        'nombre'     => trim(($o['buyer']['first_name'] ?? '') . ' ' . ($o['buyer']['last_name'] ?? '')),
        'doc'        => (string)$doc,
        'monto'      => (string)$monto,
        'items'      => (string)$items,
        'direccion'  => (string)$direccion,
        'medio_pago' => (string)$medioPago,
    ];
}

    private function convertToCsv($ordenes)
    {
        $lines = ['ID Orden,Fecha,Nombre,DNI/CUIT,Monto,Productos,Dirección,Medio de pago'];

        foreach ($ordenes as $o) {
            $line = implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $o));
            $lines[] = $line;
        }

        return implode("\n", $lines);
    }
}
