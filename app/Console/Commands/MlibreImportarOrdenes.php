<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Services\Mlibre\MlibreTokenService;
use App\Models\MlibreOrder;
use App\Models\MlibreOrderItem;
use App\Models\MlibreOrderPayment;
use App\Models\MlibreShipment;
use Illuminate\Support\Arr;

class MlibreImportarOrdenes extends Command
{
    // app/Console/Commands/MlibreImportarOrdenes.php

// arriba de la clase
protected $signature = 'mlibre:importar-ordenes
    {desde : Fecha desde (YYYY-MM-DD)}
    {hasta : Fecha hasta (YYYY-MM-DD)}
    {--estado=paid : Estado de la orden en ML}
    {--seller= : Seller ID (por defecto MLIBRE_USER_ID)}
    {--with-docs=0 : Enriquecer DNI/condición fiscal}
    {--check-fiscal-docs=0 : Chequear fiscal_documents en ML}
    {--limit=50 : Tamaño de página para /orders/search (1..50)}
';

protected $description = 'Importa/actualiza órdenes ML (pack_id, docs, shipping, pagos) de forma idempotente';

  
  public function handle()
{
    $desde   = $this->argument('desde');
    $hasta   = $this->argument('hasta');
    $estado  = $this->option('estado') ?? 'paid';
    $seller  = (int)($this->option('seller') ?: env('MLIBRE_USER_ID', 448490530));
    $withDocs= (bool)$this->option('with-docs');
    $checkFD = (bool)$this->option('check-fiscal-docs');
    $limit   = 50;

    $tokens = app(\App\Services\Mlibre\MlibreTokenService::class);

    $this->info("📦 Importando órdenes desde $desde hasta $hasta (estado: $estado, seller: $seller, limit: $limit) ...");

    $offset = 0; $ok=0; $skip=0;

    do {
        // token vigente para la página de búsqueda
        $token = $tokens->getValidAccessToken($seller);

        $resp = \Http::withHeaders([
            'Authorization' => "Bearer $token",
            'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
            'Accept'        => 'application/json',
        ])->get('https://api.mercadolibre.com/orders/search', [
            'seller' => $seller,
            'order.status' => $estado,
            'order.date_created.from' => "{$desde}T00:00:00.000-03:00",
            'order.date_created.to'   => "{$hasta}T23:59:59.000-03:00",
            'sort'   => 'date_desc',
            'limit'  => $limit,
            'offset' => $offset,
        ]);

        if (!$resp->ok()) {
            $this->error("❌ Error al consultar órdenes (offset=$offset): ".$resp->status().' '.$resp->body());
            break;
        }

        $results = $resp->json('results') ?? [];
        if (empty($results)) break;

        foreach ($results as $row) {
            $orderId = (int)$row['id'];

            try {
                // 👉 fetch con auto-refresh si 401/403
                $det = $this->fetchOrderDetail($orderId, $seller, $tokens);

                if ($withDocs) {
    [$docType,$docNum,$buyerName,$taxCond] = $this->extractBuyerDocs($det, $seller, $tokens);

    $det['_buyer_doc_type']   = $docType;
    $det['_buyer_doc_number'] = $docNum;
    $det['_buyer_name']       = $buyerName;
    $det['_buyer_tax_status'] = $taxCond;
}



                

                if ($checkFD) {
                    $det['_ml_invoice_attached'] = $this->checkFiscalDocs($det, $tokens->getValidAccessToken($seller));
                }

                $this->upsertOrderTree($seller, $det);
                $ok++;
            } catch (\Throwable $e) {
                $skip++;
                $this->warn("↷ Saltada orden {$orderId}: ".$e->getMessage());
                continue;
            }
        }

        $offset += $limit;
        $this->line("… progreso: OK {$ok} | saltadas {$skip} | offset {$offset}");
    } while (count($results) === $limit);

    $this->info("✅ Listo. Órdenes procesadas OK: {$ok} | saltadas: {$skip}");
}




    private function fetchOrderDetail(int $orderId, int $sellerId, \App\Services\Mlibre\MlibreTokenService $tokens): array
{
    $attempts = 0;
    $status   = null;

    do {
        $token = $tokens->getValidAccessToken($sellerId);

        $r = \Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
            'Accept'        => 'application/json',
        ])->get("https://api.mercadolibre.com/orders/{$orderId}");

        $status = $r->status();

        // OK
        if ($r->ok()) {
            $det = $r->json();

            // Fallback de dirección desde /shipments si hace falta
            if (empty($det['shipping']['receiver_address']) && !empty($det['shipping']['id'])) {
                try {
                    $sr = \Http::withHeaders([
                        'Authorization' => "Bearer {$tokens->getValidAccessToken($sellerId)}",
                        'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
                        'Accept'        => 'application/json',
                    ])->get("https://api.mercadolibre.com/shipments/".$det['shipping']['id']);

                    if ($sr->ok()) {
                        $det['_shipment'] = $sr->json();
                    }
                } catch (\Throwable $e) { /* tolerante */ }
            }

            return $det;
        }

        // 206/502/5xx/429 → reintentar
        if (in_array($status, [206, 429, 500, 502, 503, 504], true)) {
            $attempts++;
            usleep($attempts * 250000);
            continue;
        }

        // 401/403 → refrescar token y reintentar
        if (in_array($status, [401, 403], true)) {
            // pequeño backoff y nuevo intento con token fresco
            $attempts++;
            usleep($attempts * 250000);
            continue;
        }

        // otros códigos ⇒ no vale la pena seguir
        break;
    } while ($attempts < 3);

    throw new \RuntimeException("Orden {$orderId} HTTP {$status}");
}

/**
 * Intenta obtener doc y nombre del comprador con varias fuentes (fall-backs),
 * replicando la lógica del comando que ya te funcionaba.
 *
 * Orden de búsqueda:
 *  1) buyer.billing_info (si vino en /orders/{id})
 *  2) /orders/{id}/billing_info
 *  3) /users/{buyer_id}?attributes=identification,first_name,last_name,tags,taxpayer_type
 *  4) payments[0].payer.identification
 *  5) shipping.receiver_address.receiver_name (sólo para nombre)
 */
private function extractBuyerDocs(array $det, string $token): array
{
    $buyerId = $det['buyer']['id'] ?? null;
    $orderId = $det['id'] ?? null;

    $docType = null; $docNum = null; $buyerName = null; $taxCond = null;

    // 0) Nombre base desde la orden
    if (!empty($det['buyer']['first_name']) || !empty($det['buyer']['last_name'])) {
        $buyerName = trim(($det['buyer']['first_name'] ?? '').' '.($det['buyer']['last_name'] ?? ''));
    } elseif (!empty($det['_buyer_name'])) {
        $buyerName = $det['_buyer_name'];
    }

    // 1) buyer.billing_info (si viene embebido)
    if (!empty($det['buyer']['billing_info']['doc_number'])) {
        $docNum  = preg_replace('/\D+/', '', (string)$det['buyer']['billing_info']['doc_number']);
        $docType = strtoupper($det['buyer']['billing_info']['doc_type'] ?? (strlen($docNum)===11 ? 'CUIT' : 'DNI'));
    }
    if ($docType && $docNum) {
        return [$docType, $docNum, $buyerName, $taxCond];
    }

    // 2) /orders/{id}/billing_info
    if ($orderId) {
        try {
            $r = \Http::withToken($token)->acceptJson()
                ->get("https://api.mercadolibre.com/orders/{$orderId}/billing_info");
            if ($r->ok()) {
                $bi = $r->json();
                if (!empty($bi['doc_number'])) {
                    $docNum  = preg_replace('/\D+/', '', (string)$bi['doc_number']);
                    $docType = strtoupper($bi['doc_type'] ?? (strlen($docNum)===11 ? 'CUIT' : 'DNI'));
                }
                // nombre fiscal si existe
                if (empty($buyerName)) {
                    $buyerName = $bi['first_name'] ?? null;
                    if (!empty($bi['last_name']))  $buyerName = trim(($buyerName ? $buyerName.' ' : '').$bi['last_name']);
                    if (empty($buyerName) && !empty($bi['business_name'])) $buyerName = $bi['business_name'];
                }
                // condición fiscal (mejor esfuerzo)
                if (!empty($bi['taxpayer_type'])) $taxCond = $bi['taxpayer_type'];
            }
        } catch (\Throwable $e) { /* tolerante */ }
    }
    if ($docType && $docNum) {
        return [$docType, $docNum, $buyerName, $taxCond];
    }

    // 3) /users/{buyer_id} con identificación
    if ($buyerId) {
        try {
            $u = \Http::withToken($token)->acceptJson()
                ->get("https://api.mercadolibre.com/users/{$buyerId}", [
                    'attributes' => 'identification,first_name,last_name,taxpayer_type'
                ]);
            if ($u->ok()) {
                $ju = $u->json();
                if (!empty($ju['identification']['number'])) {
                    $docNum  = preg_replace('/\D+/', '', (string)$ju['identification']['number']);
                    $docType = strtoupper($ju['identification']['type'] ?? (strlen($docNum)===11 ? 'CUIT' : 'DNI'));
                }
                if (empty($buyerName)) {
                    $buyerName = trim(($ju['first_name'] ?? '').' '.($ju['last_name'] ?? '')) ?: $buyerName;
                }
                if (empty($taxCond) && !empty($ju['taxpayer_type'])) {
                    $taxCond = $ju['taxpayer_type']; // p.ej. 'responsable_inscripto' | 'monotributo' | 'consumidor_final'
                }
            }
        } catch (\Throwable $e) { /* tolerante */ }
    }
    if ($docType && $docNum) {
        return [$docType, $docNum, $buyerName, $taxCond];
    }

    // 4) payments[0].payer.identification
    if (!empty($det['payments'][0]['payer']['identification']['number'])) {
        $docNum  = preg_replace('/\D+/', '', (string)$det['payments'][0]['payer']['identification']['number']);
        $docType = strtoupper($det['payments'][0]['payer']['identification']['type'] ?? (strlen($docNum)===11 ? 'CUIT' : 'DNI'));
    }

    // 5) nombre desde el envío (si sigue vacío)
    if (empty($buyerName)) {
        $buyerName = $det['_shipment']['receiver_address']['receiver_name']
            ?? $det['shipping']['receiver_address']['receiver_name']
            ?? $buyerName;
    }

    return [$docType, $docNum, $buyerName, $taxCond];
}


private function checkFiscalDocs(array $det, string $token): bool
{
    // Busca documento fiscal subido al pack o a la orden.
    $packId = $det['pack_id'] ?? (int)($det['id'] ?? 0);
    $target = $packId ? "packs/{$packId}" : "orders/{$det['id']}";
    // packs/{id}/fiscal_documents es el camino esperado; si 404/403, lo damos por no-adjunto
    $r = Http::withToken($token)->acceptJson()
        ->get("https://api.mercadolibre.com/{$target}/fiscal_documents");
    return $r->ok() && !empty($r->json());
}

private function upsertOrderTree(int $sellerId, array $det): void
{
    // -------- helpers ----------
    $nn = fn($v) => !is_null($v) && $v !== '' && $v !== [];

    $preserveFactura = [
        'invoiced','arca_status','invoice_type','pos_number','invoice_number',
        'invoice_date','cae','cae_due_date','arca_invoice_id','arca_payload','arca_error',
        'ml_invoiced_by_ml','ml_invoice_attached','ml_invoice_docs_count','ml_invoice_file_id',
        'ml_invoice_checked_at','ml_note_id','ml_note_posted_at','ml_note_text'
    ];

    $safeSet = function(array $base, array $incoming, array $keys) use ($nn) {
        foreach ($keys as $k) {
            if (array_key_exists($k, $incoming) && $nn($incoming[$k])) {
                $base[$k] = $incoming[$k];
            }
        }
        return $base;
    };

    // -------- cabecera básica ----------
    $orderId = (int) ($det['id'] ?? 0);

    // fuente de shipping y dirección
    $ship      = $det['shipping']   ?? [];
    $shipExtra = $det['_shipment']  ?? [];
    $addr      = $ship['receiver_address'] ?? ($shipExtra['receiver_address'] ?? []);
    $shipStatus= $ship['status']    ?? ($shipExtra['status']    ?? null);
    $tk        = $shipExtra['tracking_number'] ?? null;
    $trk       = $shipExtra['tracking_url']    ?? null;
    $shipper   = $shipExtra['carrier'] ?? ($shipExtra['mode'] ?? null);
    $shipId    = $ship['id'] ?? ($shipExtra['id'] ?? null);

    // docs enriquecidos (si se pidieron con --with-docs)
    $docType = $det['_buyer_doc_type']   ?? ($det['buyer']['billing_info']['doc_type']   ?? ($det['buyer']['identification']['type']   ?? null));
    $docNum  = $det['_buyer_doc_number'] ?? ($det['buyer']['billing_info']['doc_number'] ?? ($det['buyer']['identification']['number'] ?? null));
    $bName   = $det['_buyer_name']       ?? trim(($det['buyer']['first_name'] ?? '').' '.($det['buyer']['last_name'] ?? '')) ?: null;
    $taxCond = $det['_buyer_tax_status'] ?? null;

    // flags ML
    $mlAdj   = $det['_ml_invoice_attached'] ?? null;

    // pack y totales
    $packId  = $det['pack_id'] ?? ($shipExtra['pack_id'] ?? null);
    $status  = $det['status'] ?? null;
    $total   = $det['total_amount'] ?? ($det['paid_amount'] ?? null);

    // buscar existente
    $existing = DB::table('mlibre_orders')
        ->where('seller_id', $sellerId)->where('order_id', $orderId)->first();

    // base con lo que YA hay (para no perder nada)
    $row = $existing ? (array)$existing : [];

    // set obligatorios
    $row['seller_id']    = $sellerId;
    $row['order_id']     = $orderId;

    // set “si viene valor”
    $row = $safeSet($row, [
        'pack_id'      => $packId,
        'status'       => $status,
        'date_created' => $det['date_created'] ?? null,
        'date_closed'  => $det['date_closed']  ?? null,
        'total_amount' => $total,
        'paid_amount'  => $det['paid_amount'] ?? null,
        'currency_id'  => $det['currency_id'] ?? null,
        'buyer_id'     => $det['buyer']['id'] ?? null,
        'buyer_name'   => $bName,
        'buyer_doc_type'   => $docType,
        'buyer_doc_number' => $docNum,
        'buyer_tax_status' => $taxCond,
        'shipping_status'          => $shipStatus,
        'shipping_tracking_number' => $tk,
        'shipping_tracking_url'    => $trk,
        'shipping_carrier'         => $shipper,
        'shipping_address'         => $addr['address_line'] ?? null,
        'shipping_id'              => $shipId,
        // si _ml_invoice_attached es boolean explícito, actualizo
        'ml_invoice_attached'      => is_bool($mlAdj) ? (int)$mlAdj : ($row['ml_invoice_attached'] ?? 0),
    ], [
        'pack_id','status','date_created','date_closed','total_amount','paid_amount','currency_id',
        'buyer_id','buyer_name','buyer_doc_type','buyer_doc_number','buyer_tax_status',
        'shipping_status','shipping_tracking_number','shipping_tracking_url',
        'shipping_carrier','shipping_address','shipping_id','ml_invoice_attached'
    ]);

    // nunca pisar campos de facturación ya presentes
    foreach ($preserveFactura as $k) {
        if ($existing && array_key_exists($k, (array)$existing) && $existing->$k !== null) {
            $row[$k] = $existing->$k;
        }
    }

    $row['updated_at'] = now();

    if ($existing) {
        DB::table('mlibre_orders')->where('id', $existing->id)->update($row);
        $orderPk = $existing->id;
    } else {
        $row['created_at'] = now();
        $orderPk = DB::table('mlibre_orders')->insertGetId($row);
    }

    // -------- items (replace-all) ----------
    DB::table('mlibre_order_items')->where('mlibre_order_id', $orderPk)->delete();
    foreach (($det['order_items'] ?? []) as $it) {
        $title = $it['title'] ?? ($it['item']['title'] ?? null);
        $variationText = null;
        if (!empty($it['variation_attributes']) && is_array($it['variation_attributes'])) {
            $parts = [];
            foreach ($it['variation_attributes'] as $a) {
                $n = $a['name'] ?? '';
                $v = $a['value_name'] ?? '';
                if ($n !== '' || $v !== '') $parts[] = "{$n}: {$v}";
            }
            $variationText = implode(' | ', $parts);
        }

        DB::table('mlibre_order_items')->insert([
            'mlibre_order_id' => $orderPk,
            'item_id'         => $it['item']['id'] ?? null,
            'title'           => $title,
            'variation_text'  => $variationText,
            'quantity'        => (int)($it['quantity'] ?? 1),
            'unit_price'      => (float)($it['unit_price'] ?? 0),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    // -------- pagos (replace-all) ----------
    DB::table('mlibre_order_payments')->where('mlibre_order_id', $orderPk)->delete();
    foreach (($det['payments'] ?? []) as $p) {
        DB::table('mlibre_order_payments')->insert([
            'mlibre_order_id'   => $orderPk,
            'payment_id'        => $p['id'] ?? null,
            'payment_type'      => $p['payment_type'] ?? ($p['payment_type_id'] ?? null),
            'status'            => $p['status'] ?? null,
            'transaction_amount'=> $p['transaction_amount'] ?? null,
            'installments'      => $p['installments'] ?? null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    // -------- RAW (idempotente) ----------
    DB::table('mlibre_orders_raw')->updateOrInsert(
        ['seller_id' => $sellerId, 'order_id' => $orderId],
        [
            'payload'   => json_encode($det, JSON_UNESCAPED_UNICODE),
            'updated_at'=> now(),
            'created_at'=> $existing ? ($existing->created_at ?? now()) : now(),
        ]
    );
}


/** 
 * Resuelve el pack_id a persistir para esta orden.
 * Regla:
 *  - Si viene el pack_id explícito → usarlo.
 *  - Si NO viene pero hay shipping_id → si el shipping agrupa 2+ órdenes,
 *    usar como pack_key el MIN(order_id) del grupo (incluyendo la actual).
 *  - Si es 1 sola orden en el shipping → dejar NULL (no es pack).
 */
private function resolvePackIdByShipping(
    int $sellerId,
    ?int $explicitPackId,
    ?int $shippingId,
    int $currentOrderId
): ?int {
    if (!empty($explicitPackId)) {
        return (int) $explicitPackId;
    }
    if (empty($shippingId)) {
        return null;
    }

    // ¿Ya hay órdenes con este shipping?
    $row = DB::table('mlibre_orders')
        ->selectRaw('MIN(order_id) AS min_order, COUNT(*) AS c, MAX(pack_id) AS some_pack')
        ->where('seller_id', $sellerId)
        ->where('shipping_id', $shippingId)
        ->first();

    if (!$row) {
        // primera orden que vemos con este shipping → aún no sabemos si será pack
        return null;
    }

    $existingCount = (int) $row->c;                  // órdenes ya existentes
    $minOrder      = (int) min($currentOrderId, (int)$row->min_order);
    $groupCount    = $existingCount + 1;             // incluyendo la actual

    if ($groupCount >= 2) {
        // Es un pack → si ya había pack_id, usalo; si no, el MIN(order_id) del grupo
        return $row->some_pack ? (int)$row->some_pack : $minOrder;
    }

    // Sigue siendo una sola orden en el shipping → sin pack
    return null;
}

/**
 * Normaliza el pack_id para TODAS las órdenes del mismo shipping:
 *  - Si el shipping tiene 2+ órdenes, todas quedan con pack_id = MIN(order_id).
 *  - Si tiene 1 sola, pack_id = NULL.
 * Se ejecuta rápido y sólo sobre el shipping_id puntual de la orden recién insertada.
 */
private function normalizePackForShipping(int $sellerId, int $shippingId): void
{
    // Subconsulta con la clave de pack y tamaño del grupo
    $sub = DB::table('mlibre_orders')
        ->selectRaw('seller_id, shipping_id, MIN(order_id) AS pack_key, COUNT(*) AS c')
        ->where('seller_id', $sellerId)
        ->where('shipping_id', $shippingId)
        ->groupBy('seller_id', 'shipping_id');

    // UPDATE con JOIN a la subconsulta
    DB::table('mlibre_orders AS o')
        ->joinSub($sub, 'g', function($join){
            $join->on('g.seller_id', '=', 'o.seller_id')
                 ->on('g.shipping_id', '=', 'o.shipping_id');
        })
        ->where('o.seller_id', $sellerId)
        ->where('o.shipping_id', $shippingId)
        ->update([
            // si c>1 → pack_key, si no → NULL
            'o.pack_id'   => DB::raw('CASE WHEN g.c > 1 THEN g.pack_key ELSE NULL END'),
            'o.updated_at'=> now(),
        ]);
}

}
