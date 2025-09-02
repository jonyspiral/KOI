<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MlibreOrder;
use App\Services\Mlibre\MlibreTokenService;

class MlibreCompletarPackIds extends Command
{
    protected $signature = 'mlibre:completar-packids 
        {--desde= : YYYY-MM-DD}
        {--hasta= : YYYY-MM-DD}
        {--seller= : seller_id (default .env MLIBRE_USER_ID)}
        {--solo-nulos=1 : sólo órdenes con pack_id NULL}
        {--dry-run=0 : no graba, sólo muestra}';

    protected $description = 'Completa pack_id: primero consultando a ML, y si no existe, usa shipping_id como agrupador';

    public function handle()
    {
        $seller = (int) ($this->option('seller') ?: env('MLIBRE_USER_ID', 448490530));
        $desde  = $this->option('desde');
        $hasta  = $this->option('hasta');
        $solo   = (bool) $this->option('solo-nulos');
        $dry    = (bool) $this->option('dry-run');

        $this->info("🔧 Completar pack_id (seller {$seller}) rango {$desde}..{$hasta} solo-nulos={$solo} dry-run={$dry}");

        $q = MlibreOrder::where('seller_id', $seller)
            ->when($desde, fn($qq)=>$qq->whereDate('date_created','>=',$desde))
            ->when($hasta, fn($qq)=>$qq->whereDate('date_created','<=',$hasta))
            ->when($solo,  fn($qq)=>$qq->whereNull('pack_id'))
            ->orderBy('id');

        $total = $q->count();
        $this->info("➡️  Candidatas: {$total}");

        if ($total === 0) return Command::SUCCESS;

        $token = app(MlibreTokenService::class)->getValidAccessToken($seller);

        $okMl = 0; $okShip = 0; $skip = 0;

        $q->chunkById(200, function($lote) use ($token, $dry, &$okMl, &$okShip, &$skip) {
            foreach ($lote as $o) {
                $old = $o->pack_id;

                // 1) Intento “oficial”: orders/{id}?attributes=pack_id
                $pack = null;
                try {
                    $r = Http::withToken($token)
                        ->acceptJson()
                        ->get("https://api.mercadolibre.com/orders/{$o->order_id}", ['attributes'=>'pack_id']);
                    if ($r->ok()) {
                        $pack = $r->json('pack_id');
                    }
                } catch (\Throwable $e) {
                    Log::warning('ML pack_id fetch fail', ['order'=>$o->order_id, 'msg'=>$e->getMessage()]);
                }

                if (!$pack) {
                    // 2) Heurística: si varias órdenes comparten shipping_id → usar shipping_id como agrupador
                    if ($o->shipping_id) {
                        $countSameShip = MlibreOrder::where('shipping_id', $o->shipping_id)->count();
                        if ($countSameShip > 1) {
                            $pack = $o->shipping_id; // no colisiona con pack_id real y nos sirve para agrupar en UI
                        }
                    }
                }

                if (!$pack || $pack == $old) {
                    $skip++;
                    continue;
                }

                if (!$dry) {
                    DB::table('mlibre_orders')
                        ->where('id', $o->id)
                        ->update(['pack_id' => $pack, 'updated_at'=>now()]);
                }

                if (is_numeric($pack) && strlen((string)$pack) > 12) {
                    $okMl++;
                } else {
                    $okShip++;
                }
            }
        });

        $this->info("✅ Seteados por ML: {$okMl} | por shipping_id: {$okShip} | sin cambios: {$skip}");
        return Command::SUCCESS;
    }
}
