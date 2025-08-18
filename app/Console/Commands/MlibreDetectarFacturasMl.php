<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use App\Models\MlibreOrder;
use App\Services\Mlibre\MlibreTokenService;
use Illuminate\Support\Facades\Log;

class MlibreDetectarFacturasMl extends Command
{
    protected $signature = 'mlibre:detectar-facturas-ml
                            {--desde= : YYYY-MM-DD}
                            {--hasta= : YYYY-MM-DD}
                            {--solo-pendientes : Solo órdenes no facturadas (invoiced=0 y ml_invoice_attached=0)}';

    protected $description = 'Detecta facturas adjuntas en ML (packs/{id}/fiscal_documents) y marca las órdenes como facturadas en ML';
private function tryMarkMlAutoInvoice(MlibreOrder $o, string $token): bool
{
    // 👇 Intento puntual: “obtener/descargar factura” para esta orden
    // (algunos sites/flows devuelven un JSON con file_id; otros devuelven el PDF directamente)
    // Probamos 2 rutas conocidas; la primera que responda 200, gana.
    $candidatas = [
        "https://api.mercadolibre.com/orders/{$o->order_id}/invoice",
        "https://api.mercadolibre.com/orders/{$o->order_id}/invoices",
    ];

    foreach ($candidatas as $url) {
        $r = Http::withToken($token)->accept('*/*')->get($url);

        // 200 con PDF o con JSON (file_id / metadata)
        if ($r->status() === 200) {
            $fileId = null;
            if (str_starts_with($r->header('content-type', ''), 'application/json')) {
                $json = $r->json();
                // heurística común: algunos devuelven { file_id, ... }
                $fileId = is_array($json) ? ($json['file_id'] ?? $json['id'] ?? null) : null;
            }

            // marcamos banderas (sin tocar invoiced de ARCA)
            $o->ml_invoiced_by_ml   = true;
            $o->ml_invoice_file_id  = $fileId;
            $o->ml_invoice_checked_at = now();

            // si además tenés columnas de adjuntos, las dejamos tal cual
            $o->save();
            return true;
        }
        // 404/403/204 → seguimos probando
    }

    return false;
}


    public function handle()
{
    $seller = (int) env('MLIBRE_USER_ID', 448490530);
    $token  = app(MlibreTokenService::class)->getValidAccessToken($seller);

    $q = MlibreOrder::where('seller_id', $seller);
    $hasAttachCols = Schema::hasColumn('mlibre_orders','ml_invoice_attached');

    if ($this->option('solo-pendientes')) {
        $q->where('invoiced', false);
        if ($hasAttachCols) $q->where('ml_invoice_attached', false);
        // además, si ya marcamos ML auto-invoice no las reviso
        if (Schema::hasColumn('mlibre_orders','ml_invoiced_by_ml')) {
            $q->where('ml_invoiced_by_ml', false);
        }
    }

    if ($d = $this->option('desde')) $q->whereDate('date_created', '>=', $d);
    if ($h = $this->option('hasta')) $q->whereDate('date_created', '<=', $h);

    $procesadas = 0; $marcadasAdj = 0; $marcadasAuto = 0;

    $this->info('🔎 Buscando facturas adjuntas y/o emitidas por ML...');
    $q->orderBy('id')->chunkById(200, function ($orders) use ($token, $hasAttachCols, &$procesadas, &$marcadasAdj, &$marcadasAuto) {
        foreach ($orders as $o) {
            $procesadas++;

            // 1) ADJUNTOS (packs/{pack_id}/fiscal_documents)
            $packId = null;
            try {
                $orderDetail = Http::withToken($token)->acceptJson()
                    ->get("https://api.mercadolibre.com/orders/{$o->order_id}");
                if ($orderDetail->ok()) $packId = $orderDetail->json('pack_id');
            } catch (\Throwable $e) {}

            $docsCount = 0;
            if ($packId) {
                $r = Http::withToken($token)->acceptJson()
                    ->get("https://api.mercadolibre.com/packs/{$packId}/fiscal_documents");
                if ($r->ok()) {
                    $json = $r->json();
                    $docs = is_array($json) ? (array)$json : [];
                    $docsCount = \is_array($docs) ? count($docs) : 0;
                }
            }

            if ($docsCount > 0 && $hasAttachCols) {
                $o->ml_invoice_attached   = true;
                $o->ml_invoice_docs_count = $docsCount;
                $o->ml_invoice_synced_at  = now();
                $o->save();
                $marcadasAdj++;
                $this->line("📎 Adjunta en ML: order {$o->order_id} (docs={$docsCount})");
                continue; // ya marcada por adjunto
            }

            // 2) AUTO ML (descarga puntual)
            if ($this->tryMarkMlAutoInvoice($o, $token)) {
                $marcadasAuto++;
                $this->line("✅ ML emitió factura (auto): order {$o->order_id}");
            }
        }
    });

    $this->info("Listo. Procesadas: {$procesadas} | Adjuntas: {$marcadasAdj} | Auto-ML: {$marcadasAuto}");
    return 0;
}
}
