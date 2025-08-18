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
    protected $signature = 'mlibre:importar-ordenes {desde} {hasta} {--estado=paid}';
    protected $description = 'Importa órdenes de Mercado Libre entre fechas y persiste en tablas para facturar';

    public function handle()
    {
        $desde   = $this->argument('desde');
        $hasta   = $this->argument('hasta');
        $estado  = $this->option('estado') ?: 'paid';
        $userId  = (int) env('MLIBRE_USER_ID', 448490530);

        $token = app(MlibreTokenService::class)->getValidAccessToken($userId);

        $this->info("📦 Importando órdenes ($estado) desde $desde hasta $hasta para seller $userId...");

        $offset = 0;
        $limit  = 50;
        $totalProcesadas = 0;

        do {
            $resp = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->get('https://api.mercadolibre.com/orders/search', [
                'seller'                  => $userId,                 // clave: alinear con caller.id
                'order.status'            => $estado,                 // normalmente 'paid'
                'order.date_created.from' => "{$desde}T00:00:00.000-03:00",
                'order.date_created.to'   => "{$hasta}T23:59:59.000-03:00",
                'sort'                    => 'date_desc',
                'limit'                   => $limit,
                'offset'                  => $offset,
            ]);

            if ($resp->failed()) {
                $this->error('❌ Error /orders/search: '.$resp->body());
                return 1;
            }

            $batch = $resp->json('results') ?? [];
            if (empty($batch)) break;

            foreach ($batch as $min) {
                $orderId = $min['id'] ?? null;
                if (!$orderId) continue;

                $ok = $this->persistOrder((string)$orderId, $userId, $token);
                if ($ok) $totalProcesadas++;
            }

            $offset += $limit;
        } while (count($batch) === $limit);

        $this->info("✅ Importación finalizada. Órdenes procesadas: $totalProcesadas");
        return 0;
    }

    private function persistOrder(string $orderId, int $sellerId, string $token): bool
    {
        // 1) /orders/{id}
        $res = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])->get("https://api.mercadolibre.com/orders/{$orderId}");

        if ($res->failed()) {
            $this->error("❌ Falló /orders/{$orderId}: ".$res->body());
            return false;
        }

        $o = $res->json();

        // —— Campos básicos
        $status       = $o['status'] ?? null;
        $dateCreated  = $o['date_created'] ?? null;
        $dateClosed   = $o['date_closed'] ?? null;
        $currency     = $o['currency_id'] ?? 'ARS';

        $buyerId      = Arr::get($o, 'buyer.id');
        $buyerName    = trim((Arr::get($o,'buyer.first_name','').' '.Arr::get($o,'buyer.last_name','')));
        $buyerDocType = Arr::get($o,'buyer.billing_info.doc_type') ?? Arr::get($o,'buyer.identification.type');
        $buyerDocNum  = Arr::get($o,'buyer.billing_info.doc_number') ?? Arr::get($o,'buyer.identification.number');

        $shippingId   = Arr::get($o,'shipping.id');
        $addrLine     = Arr::get($o,'shipping.receiver_address.address_line') ?? Arr::get($o,'shipping.receiver_address.address');
        $addrCity     = Arr::get($o,'shipping.receiver_address.city.name');
        $addrState    = Arr::get($o,'shipping.receiver_address.state.name');
        $addrZip      = Arr::get($o,'shipping.receiver_address.zip_code');

        // —— Montos
        $totalAmount  = $o['total_amount'] ?? null;
        $paidAmount   = $o['paid_amount'] ?? null;
        if ($paidAmount === null && !empty($o['payments'])) {
            $paidAmount = collect($o['payments'])->sum(fn($p) => $p['total_paid_amount'] ?? $p['transaction_amount'] ?? 0);
        }

        // —— Items normalizados
        $itemsArr = collect($o['order_items'] ?? [])->map(function ($i) {
            $title = Arr::get($i, 'item.title') ?? $i['title'] ?? (Arr::get($i,'item.id') ?? '');
            $varTxt = collect(Arr::get($i,'item.variation_attributes', Arr::get($i,'variation_attributes', [])))
                ->map(fn($a) => trim(($a['name'] ?? '').': '.($a['value_name'] ?? '')))->filter()->implode(', ');
            $quantity = (int)($i['quantity'] ?? 1);
            $unit = $i['unit_price'] ?? $i['full_unit_price'] ?? $i['sale_price'] ?? 0;

            return [
                'ml_item_id'     => Arr::get($i,'item.id'),
                'title'          => trim($title),
                'quantity'       => $quantity,
                'unit_price'     => $unit,
                'sku'            => Arr::get($i,'item.seller_sku'),
                'variation_text' => $varTxt ?: null,
                'variation'      => Arr::get($i,'item.variation_attributes', Arr::get($i,'variation_attributes')),
                // totales por ítem (si no vienen, dejamos null y calculamos en facturación)
                'total_amount'   => Arr::get($i,'total_amount'),
                'net_amount'     => Arr::get($i,'net_amount'),
                'vat_rate'       => Arr::get($i,'vat_rate'),
                'vat_amount'     => Arr::get($i,'vat_amount'),
            ];
        })->values()->all();

        // —— Payments normalizados
        $paymentsArr = collect($o['payments'] ?? [])->map(function ($p) {
            return [
                'payment_id'         => (string)($p['id'] ?? ''),
                'status'             => $p['status'] ?? null,
                'payment_type'       => $p['payment_type'] ?? ($p['payment_type_id'] ?? null),
                'payment_method_id'  => $p['payment_method_id'] ?? null,
                'transaction_amount' => $p['transaction_amount'] ?? null,
                'total_paid_amount'  => $p['total_paid_amount'] ?? null,
                'fee_amount'         => $p['fee_amount'] ?? null,
                'installments'       => $p['installments'] ?? null,
                'date_approved'      => $p['date_approved'] ?? null,
            ];
        })->values()->all();

        // —— Fallback de dirección vía /shipments si hace falta
        $shipmentRaw = null;
        if (!$addrLine && $shippingId) {
            $s = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->get("https://api.mercadolibre.com/shipments/{$shippingId}");
            if ($s->ok()) {
                $shipmentRaw = $s->json();
                $addrLine  = Arr::get($shipmentRaw,'receiver_address.address_line')
                             ?? trim((Arr::get($shipmentRaw,'receiver_address.street_name','').' '.(Arr::get($shipmentRaw,'receiver_address.street_number',''))));
                $addrCity  = Arr::get($shipmentRaw,'receiver_address.city.name', $addrCity);
                $addrState = Arr::get($shipmentRaw,'receiver_address.state.name', $addrState);
                $addrZip   = Arr::get($shipmentRaw,'receiver_address.zip_code', $addrZip);
            }
        }

        // —— Persistencia (idempotente): upsert cabecera + reemplazo hijos
        DB::beginTransaction();
        try {
            // RAW
            DB::table('mlibre_orders_raw')->updateOrInsert(
                ['seller_id' => $sellerId, 'order_id' => $o['id']],
                ['payload' => json_encode($o), 'pulled_at' => now()]
            );

            // CABECERA (upsert por seller_id + order_id)
            $order = MlibreOrder::query()->where('seller_id',$sellerId)->where('order_id',$o['id'])->first();
            if (!$order) $order = new MlibreOrder();

            $order->fill([
                'seller_id'        => $sellerId,
                'order_id'         => $o['id'],
                'status'           => $status,
                'date_created'     => $dateCreated,
                'date_closed'      => $dateClosed,
                'total_amount'     => $totalAmount,
                'paid_amount'      => $paidAmount,
                'currency_id'      => $currency,
                'buyer_id'         => $buyerId,
                'buyer_name'       => $buyerName,
                'buyer_doc_type'   => $buyerDocType,
                'buyer_doc_number' => $buyerDocNum,
                'shipping_id'      => $shippingId,
                'address_line'     => $addrLine,
                'city'             => $addrCity,
                'state'            => $addrState,
                'zip_code'         => $addrZip,
                'items_count'      => count($itemsArr),
                'payments_count'   => count($paymentsArr),
                'tags'             => $o['tags'] ?? null,
                // campos de facturación quedan como estén (invoiced/arca_status no se tocan aquí)
            ]);
            $order->save();

            // ITEMS
            MlibreOrderItem::where('mlibre_order_id', $order->id)->delete();
            foreach ($itemsArr as $it) {
                $order->items()->create($it);
            }

            // PAYMENTS
            MlibreOrderPayment::where('mlibre_order_id', $order->id)->delete();
            foreach ($paymentsArr as $p) {
                $order->payments()->create($p);
            }

            // SHIPMENTS (reemplazo simple: uno por shipment_id si existe)
            MlibreShipment::where('mlibre_order_id', $order->id)->delete();
            if ($shippingId) {
                $order->shipments()->create([
                    'shipment_id'    => $shippingId,
                    'status'         => Arr::get($o,'shipping.status'),
                    'service'        => Arr::get($o,'shipping.shipping_type'),
                    'tracking_number'=> Arr::get($o,'shipping.tracking_number'),
                    'address_line'   => $addrLine,
                    'street_name'    => Arr::get($shipmentRaw,'receiver_address.street_name'),
                    'street_number'  => (string)Arr::get($shipmentRaw,'receiver_address.street_number'),
                    'city'           => $addrCity,
                    'state'          => $addrState,
                    'zip_code'       => $addrZip,
                    'raw'            => $shipmentRaw,
                ]);
            }

            DB::commit();
            $this->line("✅ Orden {$o['id']} persistida");
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("❌ Persistencia orden {$o['id']}: ".$e->getMessage());
            return false;
        }
    }
}
