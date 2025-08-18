<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;   // 👈 IMPORTANTE
use Illuminate\Support\Arr;
use App\Models\MlibreOrder;
use App\Services\Mlibre\MlibreTokenService;

class MlibreCompletarDocs extends Command
{
    protected $signature = 'mlibre:completar-docs {--desde=} {--hasta=}';
    protected $description = 'Completa DNI/CUIT de órdenes usando /orders/{id}/billing_info';

    public function handle()
    {
        $desde  = $this->option('desde');
        $hasta  = $this->option('hasta');
        $seller = (int) env('MLIBRE_USER_ID', 448490530);
        $token  = app(MlibreTokenService::class)->getValidAccessToken($seller);

        $q = MlibreOrder::where('seller_id', $seller)
            ->where(function($qq){
                $qq->whereNull('buyer_doc_number')->orWhere('buyer_doc_number', '');
            });

        if ($desde) $q->whereDate('date_created', '>=', $desde);
        if ($hasta) $q->whereDate('date_created', '<=', $hasta);

        $actualizados = 0;

        $q->orderBy('id')->chunkById(200, function($orders) use ($token, &$actualizados) {
            foreach ($orders as $o) {
                $res = Http::withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
                    'Accept'        => 'application/json',
                ])->get("https://api.mercadolibre.com/orders/{$o->order_id}/billing_info");

                if (!$res->ok()) {
                    continue;
                }

                $b = $res->json();

                $docType = Arr::get($b,'doc_type')      ?? Arr::get($b,'billing_info.doc_type');
                $docNum  = Arr::get($b,'doc_number')    ?? Arr::get($b,'billing_info.doc_number');
                $name    = Arr::get($b,'name')          ?? Arr::get($b,'billing_info.name') ?? $o->buyer_name;
                $taxCond = Arr::get($b,'taxpayer_type') ?? Arr::get($b,'billing_info.taxpayer_type');

                if ($docNum) {
                    $o->buyer_doc_type   = $docType ?: $o->buyer_doc_type;
                    $o->buyer_doc_number = $docNum;
                    $o->buyer_name       = $name;

                    // 👇 Solo si existe la columna
                    if (Schema::hasColumn('mlibre_orders', 'buyer_tax_status')) {
                        $o->buyer_tax_status = $taxCond;
                    }

                    $o->save();
                    $actualizados++;
                }
            }
        });

        $this->info("✅ Documentos completados: {$actualizados}");
        return 0;
    }
}
