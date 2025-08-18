<?php

namespace App\Http\Controllers\Mlibre;

use App\Http\Controllers\Controller;
use App\Models\MlibreOrder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ArcaFacturarLog;         // si tu modelo tiene otro nombre, ajustalo
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Carbon;

use App\Services\Mlibre\MlibreTokenService;

class OrdersController extends Controller
{
    // opcional: $this->middleware('auth');


public function index(Request $request)
{
    $from       = $request->get('from');   // si ya los usabas, se conservan
    $to         = $request->get('to');
    $onlyPaid   = $request->boolean('only_paid', false);
    $caeFilter  = $request->get('cae', 'any');          // any | con | sin
    $arcaFilter = $request->get('arca_status', 'any');  // any | success | error | processing | pending

    $q = MlibreOrder::query()
        ->when($from, fn($qq)=>$qq->whereDate('date_created','>=',$from))
        ->when($to,   fn($qq)=>$qq->whereDate('date_created','<=',$to))
        ->when($onlyPaid, fn($qq)=>$qq->where('status','paid'))
        // 👇 Filtros nuevos
        ->when($caeFilter === 'con', fn($qq)=>$qq->whereNotNull('cae')->where('cae','<>',''))
        ->when($caeFilter === 'sin', fn($qq)=>$qq->where(function($w){
            $w->whereNull('cae')->orWhere('cae','');
        }))
        ->when($arcaFilter !== 'any', fn($qq)=>$qq->where('arca_status',$arcaFilter))
        // Eager loading del último log para evitar N+1
        ->with([
            'items','payments',
            'logs' => fn(HasMany $l)=>$l->latest()->limit(1),
        ])
        ->orderByDesc('date_created');

    // si ya venías paginando, dejalo igual
    $orders = $q->paginate(50)->withQueryString();

    return view('mlibre.orders.index', [
        'orders'     => $orders,
        'from'       => $from,
        'to'         => $to,
        'onlyPaid'   => $onlyPaid,
        'caeFilter'  => $caeFilter,
        'arcaFilter' => $arcaFilter,
    ]);
}


   public function facturarSeleccionados(Request $request)
{
    $ids = $request->input('order_ids', []);
    if (!is_array($ids) || empty($ids)) {
        return back()->withErrors('No seleccionaste ninguna orden.');
    }

    $orders = MlibreOrder::with(['items','payments'])
        ->whereIn('id', $ids)->get();

    $ok = 0; $err = 0;

    foreach ($orders as $o) {
        // Elegibilidad estricta en backend (igual que en el Blade)
        $mlHasInvoice   = (bool) ($o->ml_invoice_attached ?? false);
        $mlAutoInvoice  = (bool) ($o->ml_invoiced_by_ml ?? false);
        $yaFacturada    = (bool) ($o->invoiced ?? false);
        $eligible       = ($o->status === 'paid' && !$yaFacturada && !$mlHasInvoice && !$mlAutoInvoice);

        if (!$eligible) {
            continue;
        }

        // ⏳ Marcar "processing" antes de ejecutar el comando (trazabilidad server-side)
        $o->arca_status = 'processing';
        $o->save();

        // ---- Parámetros para el comando arca:facturar ----
        [$docTipoAfip, $docNroAfip] = $this->mapDocToAfip($o->buyer_doc_type, $o->buyer_doc_number);
        $tipoCbte = ($docTipoAfip === 80) ? 1 : 6;  // 1=A, 6=B (mínimo viable)
        $pto      = (int) (config('arca.pv', 7));   // PV habilitado WS
        $ali      = 21;                             // IVA estándar
        $total    = (float) ($o->total_amount ?? $o->paid_amount ?? 0);

        if ($total <= 0) {
            $o->arca_status = 'error';
            $o->save();
            $this->storeArcaLog($o, 'error', ['argv'=>[]], null, 'Total no válido (<= 0)');
            $err++;
            continue;
        }

        $montoCmd = ($tipoCbte === 1)
            ? round($total / (1 + $ali/100), 2) // A: neto
            : round($total, 2);                  // B: total con IVA

        $argv = [
            'tipo'      => $tipoCbte,
            'monto'     => $montoCmd,
            '--pto'     => $pto,
            '--docTipo' => $docTipoAfip,
            '--docNro'  => $docNroAfip,
        ];

        try {
            $exitCode = \Artisan::call('arca:facturar', $argv);
            $out = \Artisan::output(); // stdout del comando

            // 🔍 Parseo robusto: Resultado / CAE / Vto / PV / Nro (soporta "nro=" del print)
            $parsed = $this->parseArcaOutput($out);
            $aprobada = (bool) ($parsed['aprobada'] ?? false);
            $cae      = $parsed['cae'] ?? null;

            if ($aprobada && $cae) {
                // ✅ Persistimos datos fiscales
                $pvFinal = (int) ($parsed['pv'] ?? $pto);
                $o->invoiced       = true;
                $o->arca_status    = 'success';
                $o->invoice_type   = ($tipoCbte === 1) ? 'A' : 'B';
                $o->pos_number     = str_pad((string)$pvFinal, 4, '0', STR_PAD_LEFT); // 4 dígitos
                if (empty($o->invoice_number) && !empty($parsed['cbte'])) {
                    $o->invoice_number = (int) $parsed['cbte'];
                }
                $o->invoice_date   = now();
                $o->cae            = $cae;
                $o->cae_due_date   = $parsed['vto'] ?? null;
                $o->save();

                // 🔁 Confirmación en AFIP (si existe el helper y tenemos nro)
                if (method_exists($this, 'confirmarCaeEnAfip')) {
                    $cbteNum = (int) ($parsed['cbte'] ?? $o->invoice_number ?? 0);
                    if ($cbteNum > 0) {
                        $confirm = $this->confirmarCaeEnAfip($tipoCbte, $pvFinal, $cbteNum);
                        if (empty($confirm['ok'])) {
                            $o->arca_status = 'warning';
                            $o->save();
                            $this->storeArcaLog(
                                $o,
                                'warning',
                                ['argv'=>$argv],
                                ['stdout'=>$out, 'confirm'=>$confirm],
                                'CAE no confirmó en AFIP PROD (ambiente/pto/cuit?)'
                            );
                        }
                    }
                }

                // 📝 Log de auditoría
                $this->storeArcaLog(
                    $o,
                    'success',
                    ['argv' => $argv],
                    ['stdout' => $out, 'exitCode' => $exitCode, 'parsed' => $parsed],
                    null
                );

                // 🔗 (no bloqueante) sincronizar nota/adjunto en ML
                try { $this->sincronizarEnML($o, null); } catch (\Throwable $e) {}

                $ok++;
            } else {
                // ❌ No aprobada o sin CAE
                $o->arca_status = 'error';
                $o->save();

                $this->storeArcaLog(
                    $o,
                    'error',
                    ['argv' => $argv],
                    ['stdout' => $out, 'exitCode' => $exitCode, 'parsed' => $parsed],
                    'No aprobado o sin CAE'
                );

                \Log::warning('ARCA no aprobada / sin CAE', [
                    'order'=>$o->order_id,'out'=>$out,'code'=>$exitCode
                ]);
                $err++;
            }

        } catch (\Throwable $e) {
            $o->arca_status = 'error';
            $o->save();

            $this->storeArcaLog(
                $o,
                'error',
                ['argv' => $argv],
                null,
                $e->getMessage()
            );

            \Log::error('Excepción facturando con ARCA (artisan)', [
                'order' => $o->order_id, 'msg' => $e->getMessage()
            ]);
            $err++;
        }
    }

    return back()->with('status', "Facturación ejecutada. OK: {$ok} | Error: {$err}");
}


private function storeArcaLog(MlibreOrder $o, string $status, array $req = [], ?array $resp = null, ?string $err = null): void
{
    $row = [
        'status'        => $status,                              // success | error
        'request_json'  => $req ? json_encode($req) : null,
        'response_json' => $resp ? json_encode($resp) : null,
        'error_message' => $err,
        'created_at'    => now(),
        'updated_at'    => now(),
    ];

    // seteamos FK según columnas existentes
    if (Schema::hasColumn('arca_facturar_logs','order_id')) {
        $row['order_id'] = $o->order_id;           // id de orden de ML
    }
    if (Schema::hasColumn('arca_facturar_logs','mlibre_order_id')) {
        $row['mlibre_order_id'] = $o->id;          // id interno
    }
    if (Schema::hasColumn('arca_facturar_logs','seller_id')) {
        $row['seller_id'] = $o->seller_id ?? null;
    }

    DB::table('arca_facturar_logs')->insert($row);
}



   // 👇 acá va
private function buildArcaPayloadFromOrder(MlibreOrder $o): array
{
    [$docTipoAfip, $docNroAfip] = $this->mapDocToAfip($o->buyer_doc_type, $o->buyer_doc_number);

    $tipo = ($docTipoAfip === 80) ? 1 : 6;            // 1=A, 6=B
    $pto  = (int) (config('arca.pv', 7));
    $total = (float) ($o->total_amount ?? $o->paid_amount ?? 0);
    $ali   = 21;

    $items = [];
    foreach ($o->items as $it) {
        $desc = trim(($it->title ?? $it->item_title ?? 'Item ML') .
                     (isset($it->variation_text) ? " ({$it->variation_text})" : ''));
        $qty  = (int) ($it->quantity ?? 1);
        $unit = (float) ($it->unit_price ?? 0);
        $items[] = ['descripcion'=>$desc,'cantidad'=>$qty,'precio_unit'=>$unit];
    }

    $cond = $this->mapCondIva($o->buyer_tax_status ?? null, $docTipoAfip);

    if ($tipo === 1) { // A: neto + IVA
        $neto = round($total / (1 + $ali/100), 2);
        return [
            'tipoComprobante' => 1,
            'ptoVta'          => $pto,
            'docTipo'         => $docTipoAfip,
            'docNro'          => $docNroAfip,
            'condIva'         => $cond,
            'alicuota'        => $ali,
            'neto'            => $neto,
            'items'           => $items,
            'meta'            => ['ml_order_id' => $o->order_id],
        ];
    }

    // B: total con IVA
    return [
        'tipoComprobante' => 6,
        'ptoVta'          => $pto,
        'condIva'         => $cond,
        'alicuota'        => $ali,
        'total'           => round($total, 2),
        'docTipo'         => $docTipoAfip,
        'docNro'          => $docNroAfip,
        'items'           => $items,
        'meta'            => ['ml_order_id' => $o->order_id],
    ];
}

private function mapDocToAfip(?string $tipo, ?string $numero): array
{
    $n = preg_replace('/\D+/', '', (string)$numero);
    $map = ['CUIT'=>80,'CUIL'=>80,'DNI'=>96,'LE'=>89,'LC'=>90,'CI'=>87,'PAS'=>94];
    $docTipoAfip = $map[strtoupper((string)$tipo)] ?? 99; // 99=CF
    if ($docTipoAfip === 99) $n = '0';
    if ($docTipoAfip === 80 && strlen($n) !== 11) $docTipoAfip = 96; // si CUIT inválido → DNI
    return [$docTipoAfip, (int)$n];
}

private function mapCondIva(?string $taxStatus, int $docTipoAfip): int
{
    $tax = strtoupper((string)$taxStatus);
    if (in_array($tax, ['RI','RESPONSABLE_INSCRIPTO','RESPONSABLE INSCRIPTO'], true) || $docTipoAfip === 80) {
        return 1; // Responsable Inscripto
    }
    return 5; // Consumidor Final (ajustá si querés mapear Monotributo distinto)
}

   private function sincronizarEnML(\App\Models\MlibreOrder $order, ?string $pdfPath = null): array
{
    $result = ['note_ok'=>false,'message_ok'=>false,'note_status'=>null,'message_status'=>null,'note_body'=>null,'message_body'=>null];

    try {
        $doNotes  = (bool) env('ML_SYNC_NOTES', true);
        $token    = app(MlibreTokenService::class)->getValidAccessToken($order->seller_id);
        $appId    = env('MLIBRE_APP_ID');
        $ua       = 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)';

        // 🛡️ idempotencia básica: si ya posteamos una nota con este texto, no repetir
        $noteText = $this->buildFacturaNote($order);
        if ($doNotes && $order->ml_note_id && trim((string)$order->ml_note_text) === trim($noteText)) {
            // ya está, marcamos ok y salimos
            $result['note_ok']     = true;
            $result['note_status'] = 208; // Already Reported (marcador interno)
            return $result;
        }

        // A) Nota interna
        if ($doNotes) {
            $resp = Http::withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'User-Agent'    => $ua,
                    'Accept'        => 'application/json',
                    'X-Client-Id'   => $appId,
                ])->post("https://api.mercadolibre.com/orders/{$order->order_id}/notes", ['note' => $noteText]);

            $result['note_status'] = $resp->status();
            $result['note_body']   = $resp->body();

            if ($resp->successful()) {
                $result['note_ok'] = true;

                // Guardar id y fechas (evita duplicar en próximos intentos)
                $nid   = $resp->json('note.id');
                $nwhen = $resp->json('note.date_created'); // ej: 2025-08-17T23:48:19Z

                $order->ml_note_id        = $nid ?: $order->ml_note_id;
                $order->ml_note_text      = $noteText;
                $order->ml_note_posted_at = $nwhen ? Carbon::parse($nwhen) : now();
                $order->save();
            } else {
                Log::warning('ML: fallo al crear nota interna', [
                    'order_id'=>$order->order_id,
                    'http'=>$resp->status(),
                    'body'=>$resp->body(),
                ]);
            }
        }

        // (…resto de tu método: adjuntos/mensajes, fallback, storeArcaLog, etc.)
        // deja tal cual lo que ya tenías

    } catch (\Throwable $e) {
        Log::warning('ML sync post-factura: excepción no crítica', [
            'order_id' => $order->order_id,
            'msg'      => $e->getMessage(),
        ]);
    }

    // (opcional) storeArcaLog del snapshot ml_sync (lo dejás como ya lo pusimos)
    try {
        $this->storeArcaLog(
            $order,
            ($result['note_ok'] || $result['message_ok']) ? 'success' : 'error',
            ['ml_sync'=>'request'],
            ['ml_sync'=>$result],
            null
        );
    } catch (\Throwable $e) {}

    return $result;
}

private function buildFacturaNote(\App\Models\MlibreOrder $o): string
{
    $num = str_pad((string)($o->invoice_number ?? ''), 8, '0', STR_PAD_LEFT);
    $pos = $o->pos_number ?? '';
    $tipo = $o->invoice_type ?? '';
    // normalizar fecha vto si viene como AAAAMMDD
    $vto  = $o->cae_due_date;
    if ($vto && preg_match('/^\d{8}$/', $vto)) {
        $vto = substr($vto,0,4).'-'.substr($vto,4,2).'-'.substr($vto,6,2);
    }
    return "Factura {$tipo} {$pos}-{$num} | CAE {$o->cae}".($vto ? " (vto {$vto})" : '');
}


/**
 * Sube el PDF de la factura al detalle (fiscal_documents).
 * Requiere que la cuenta/site soporte este recurso.
 */
    private function mlibreUploadInvoice($orderId, string $pdfPath, string $token): void
{
    // 1) obtener pack_id (si no hay, caer a order_id)
    $packId = null;
    try {
        $res = Http::withToken($token)
            ->acceptJson()
            ->get("https://api.mercadolibre.com/orders/{$orderId}", ['attributes' => 'pack_id']);
        if ($res->ok()) {
            $packId = $res->json('pack_id');
        }
    } catch (\Throwable $e) {
        // no bloquear
    }
    $targetId = $packId ?: $orderId;

    // 2) subir PDF (multipart) como documento fiscal
    $resp = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
        ])
        ->attach('fiscal_document', file_get_contents($pdfPath), basename($pdfPath))
        ->post("https://api.mercadolibre.com/packs/{$targetId}/fiscal_documents");

    if (!$resp->ok()) {
        Log::warning('ML: fallo al subir factura', [
            'order_id' => $orderId,
            'pack_id'  => $packId,
            'status'   => $resp->status(),
            'body'     => $resp->body(),
        ]);
    }
}
/**
 * Envía el PDF por mensajería post-venta (adjunto).
 * Flujo: subir adjunto → enviar mensaje referenciando la orden.
 */
private function mlibreEnviarMensajeConAdjunto($orderId, string $pdfPath, string $token): void
{
    // A) subir adjunto
    $upload = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
        ])
        ->attach('file', file_get_contents($pdfPath), basename($pdfPath))
        ->post('https://api.mercadolibre.com/messages/attachments');

    if (!$upload->ok()) {
        Log::warning('ML: fallo upload attachment', [
            'order_id' => $orderId,
            'status'   => $upload->status(),
            'body'     => $upload->body(),
        ]);
        return;
    }

    $attachmentId = $upload->json('id');
    if (!$attachmentId) return;

    // B) enviar mensaje referenciando la ORDEN
    $appId  = env('MLIBRE_APP_ID');
    $siteId = env('ML_SITE_ID', 'MLA');
    $seller = (int) env('MLIBRE_USER_ID', 448490530);

    $body = [
        'from' => ['user_id' => $seller],
        'to'   => [[
            'resource'    => 'orders',
            'resource_id' => (string) $orderId,
            'site_id'     => $siteId,
        ]],
        'text' => ['plain' => 'Adjuntamos su factura electrónica.'],
        'attachments' => [$attachmentId],
    ];

    $resp = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'X-Client-Id'   => $appId, // requerido por ML
            'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
        ])->post("https://api.mercadolibre.com/messages?application_id={$appId}", $body);

    if (!$resp->ok()) {
        Log::warning('ML: fallo enviar mensaje con adjunto', [
            'order_id' => $orderId,
            'status'   => $resp->status(),
            'body'     => $resp->body(),
        ]);
    }
}
// ✅ CONFIRMACIÓN POST-CAE (tolerante)
// - Si no existe el servicio o el método, y ARCA_STRICT_CONFIRM = false → considera OK (skip)
// - Si ARCA_STRICT_CONFIRM = true → devuelve error si no puede confirmar
private function confirmarCaeEnAfip(int $tipo, int $pto, int $nro): array
{
    if ($nro <= 0) {
        return ['ok' => false, 'msg' => 'Número de comprobante inválido'];
    }

    try {
        // ¿tenemos el servicio?
        if (class_exists(\App\Services\Arca\ArcaWsfeHttpService::class)) {
            $ws = app(\App\Services\Arca\ArcaWsfeHttpService::class);

            // ¿tiene el método?
            if (method_exists($ws, 'feCompConsultar')) {
                $r = $ws->feCompConsultar($tipo, $pto, $nro);
                // esperable: ['ok'=>true, 'data'=>...]
                return (is_array($r) && !empty($r['ok']))
                    ? ['ok' => true, 'data' => $r]
                    : ['ok' => false, 'data' => $r];
            }
        }

        // Si llegamos acá, no hay método implementado.
        if (!env('ARCA_STRICT_CONFIRM', false)) {
            \Log::notice('AFIP confirm skipped (no feCompConsultar).', compact('tipo','pto','nro'));
            return ['ok' => true, 'skipped' => true];
        }

        return ['ok' => false, 'msg' => 'Servicio feCompConsultar no disponible'];

    } catch (\Throwable $e) {
        return ['ok' => false, 'msg' => $e->getMessage()];
    }
}

private function parseArcaOutput(string $out): array
{
    $aprobada = (bool) preg_match('/Resultado:\s*A\b/i', $out);

    $cae = null; $vto = null; $pv = null; $cbte = null; $tipo = null; $env = null; $cuit = null;

    // CAE + Vto
    if (preg_match('/CAE:\s*([0-9]+)\s+Vto:\s*([0-9]{4}-[0-9]{2}-[0-9]{2}|[0-9\/\-]+)/i', $out, $m)) {
        $cae = $m[1] ?? null;
        $vto = $m[2] ?? null;
    }

    // Formato clásico "Cbte: 0001-00001234"
    if (preg_match('/Cbte:\s*(\d{4})-(\d{8})/i', $out, $m)) {
        $pv   = ltrim($m[1] ?? '', '0');
        $cbte = ltrim($m[2] ?? '', '0');
    }
    // Alternativa "PtoVta: 7, Cbte: 1234"
    elseif (preg_match('/PtoVta:\s*(\d+).*?Cbte:\s*(\d+)/i', $out, $m)) {
        $pv   = $m[1] ?? null;
        $cbte = $m[2] ?? null;
    }
    // ⚠️ Nuevo: línea de tu comando "Emitiendo tipo=6, pto=7, nro=1512"
    elseif (preg_match('/Emitiendo\s+tipo=\s*(\d+)\s*,\s*pto=\s*(\d+)\s*,\s*nro=\s*(\d+)/i', $out, $m)) {
        $tipo = $m[1] ?? null;
        $pv   = $m[2] ?? null;
        $cbte = $m[3] ?? null;
    }

    // (Opcional) Ambiente y CUIT si el comando los imprime
    if (preg_match('/Ambiente:\s*([a-zA-Z]+)/i', $out, $m)) $env = strtolower($m[1] ?? '');
    if (preg_match('/CUIT:\s*(\d{11})/i', $out, $m)) $cuit = $m[1] ?? null;

    return compact('aprobada','cae','vto','pv','cbte','tipo','env','cuit');
}

}
