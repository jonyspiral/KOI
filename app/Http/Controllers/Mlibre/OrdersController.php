<?php
namespace App\Http\Controllers\Mlibre;

use App\Http\Controllers\Controller;
use App\Models\MlibreOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\Mlibre\MlibreTokenService;
use App\Services\Arca\ArcaWsaaHttpService;
use App\Services\Arca\ArcaWsfeHttpService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class OrdersController extends Controller
{
    /** LISTADO (sin cambios sustanciales) */
    public function index(Request $request)
    {
        $from       = $request->get('from');
        $to         = $request->get('to');
        $onlyPaid   = $request->boolean('only_paid', false);
        $caeFilter  = $request->get('cae', 'any');          // any|con|sin
        $arcaFilter = $request->get('arca_status', 'any');  // any|success|error|warning|processing|pending

        $q = MlibreOrder::query()
            ->when($from, fn($qq)=>$qq->whereDate('date_created','>=',$from))
            ->when($to,   fn($qq)=>$qq->whereDate('date_created','<=',$to))
            ->when($onlyPaid, fn($qq)=>$qq->where('status','paid'))
            ->when($caeFilter === 'con', fn($qq)=>$qq->whereNotNull('cae')->where('cae','<>',''))
            ->when($caeFilter === 'sin', fn($qq)=>$qq->where(function($w){ $w->whereNull('cae')->orWhere('cae',''); }))
            ->when($arcaFilter !== 'any', fn($qq)=>$qq->where('arca_status',$arcaFilter))
            ->with([
                'items','payments',
                'logs' => fn(HasMany $l)=>$l->latest()->limit(1),
            ])
            ->orderByDesc('date_created');

        $orders = $q->paginate(50)->withQueryString();

        // Agrupar por pack u orden (igual que lo tenías)
        $groups = collect($orders->items())
            ->groupBy(fn($o) => $o->pack_id ? ('pack:'.$o->pack_id) : ('order:'.$o->order_id));

        return view('mlibre.orders.index', compact('orders','groups','from','to','onlyPaid','caeFilter','arcaFilter'));
    }

    /** FACTURAR (lote + individual via formaction a la misma ruta) */
    public function facturarSeleccionados(Request $request)
    {
        $ids = $request->input('order_ids', []);
        if (!is_array($ids) || empty($ids)) {
            return back()->withErrors('No seleccionaste ninguna orden.');
        }

        // WSAA TA (token/sign/cuit) — 1 vez para todo el lote
      $wsfe = app(\App\Services\Arca\ArcaWsfeHttpService::class);
[$t,$s,$c] = $this->obtenerTA($wsfe);


        // Defaults (podés parametrizar desde la vista si querés)
        $pto       = (int)($request->input('pto', 7));
        $ali       = (float)($request->input('ali', 21.0));
        $cond      = (int)($request->input('cond', 5)); // 5 = Consumidor Final

        $orders = MlibreOrder::with(['items','payments'])
            ->whereIn('id', $ids)->get();

        $ok=0; $err=0;

        foreach ($orders as $o) {
            $mlHas  = (bool) ($o->ml_invoice_attached ?? false);
            $mlAuto = (bool) ($o->ml_invoiced_by_ml ?? false);
            $yaFact = (bool) ($o->invoiced ?? false);
            $eligible = ($o->status === 'paid' && !$yaFact && !$mlHas && !$mlAuto);
            if (!$eligible) continue;

            try {
                // Doc del receptor (tu helper ya lo mapea a AFIP)
                [$docTipo, $docNro] = $this->mapDocToAfip($o->buyer_doc_type ?? null, $o->buyer_doc_number ?? null);

                // Monto total con IVA (como venís usando)
                $monto = (float)($o->total_amount ?? $o->paid_amount ?? 0);
                if ($monto <= 0) throw new \RuntimeException('Monto inválido');

                // Tipo de comprobante según reglas mínimas: CUIT→A, sino→B (ajustable)
                // $tipo = ($docTipo === 80) ? 1 : 6;

                // 1) Conseguir número protegido por lock (por (pto,tipo))
                // Detectar condición del receptor y tipo de comprobante correcto
                [$condDetectado, $tipo] = $this->resolverCondYTipo($wsfe, $docTipo, $docNro);

                // Si querés permitir override manual desde el form, respetalo:
                $cond = (int) ($request->input('cond', $cond ?? $condDetectado));

                // Validación básica (RG 5616): si vamos a A, cond debe ser 1
                if ($tipo === 1 && $cond !== 1) {
                    // Fallback automático a B si no es RI
                    $tipo = 6;
                }


                $nro = $this->reservarNumeroComprobante($wsfe, [$t,$s,$c], $pto, $tipo);

                // 2) Solicitar CAE directo por servicio (sin Artisan)
                if ($tipo === 6) {
                    $resp = $wsfe->solicitarCaeFacturaB([$t,$s,$c], $pto, $nro, $this->nf($monto), $cond, $ali, $docTipo, (int)$docNro);
                } else {
                    // Para A, el servicio ya está implementado en tu stack
                    // - neto = total / (1 + ali/100)
                    $neto = round($monto / (1 + ($ali/100)), 2);
                    $resp = $wsfe->solicitarCaeFacturaA([$t,$s,$c], $pto, $nro, $this->nf($neto), $ali, $docTipo, (int)$docNro, 1);
                }

                $aprobada = ($resp['resultado'] ?? null) === 'A';
                $cae      = $resp['cae'] ?? null;
                $vto      = $this->normalizarFechaVto($resp['vto'] ?? null);

                if ($aprobada && $cae) {
                    // Persistencia local (igual que ya hacías)
                    $o->invoiced      = true;
                    $o->arca_status   = 'success';
                    $o->invoice_type  = ($tipo === 1) ? 'A' : 'B';
                    $o->pos_number    = $pto;
                    $o->invoice_number= $nro;
                    $o->invoice_date  = now()->toDateString();
                    $o->cae           = $cae;
                    $o->cae_due_date  = $vto;
                    $o->save();

                    // Log success
                    $this->storeArcaLog($o, 'success',
                        ['tipo'=>$tipo,'pto'=>$pto,'nro'=>$nro,'monto'=>$monto,'ali'=>$ali,'cond'=>$cond,'docTipo'=>$docTipo,'docNro'=>$docNro],
                        ['resultado'=>'A','cae'=>$cae,'vto'=>$vto]
                    );

                    // Confirmación AFIP opcional (estricta) si está habilitada y disponible
                    if (config('arca.strict_confirm', env('ARCA_STRICT_CONFIRM', false))
                        && method_exists($wsfe, 'feCompConsultar')) {
                        try {
                            $conf = $wsfe->feCompConsultar([$t,$s,$c], $pto, $tipo, $nro);
                            if (($conf['resultado'] ?? '') !== 'A') {
                                $o->arca_status = 'warning'; $o->save();
                            }
                        } catch (\Throwable $e) {
                            Log::warning('ARCA strict confirm falló', ['id'=>$o->id,'msg'=>$e->getMessage()]);
                        }
                    }

                    // Nota post‑factura en ML (flags .env)
                    try { $this->sincronizarEnML($o, null); } catch (\Throwable $e) {}

                    $ok++;
                } else {
                    $o->arca_status = 'error';
                    $o->arca_error  = trim(($resp['errCode'] ?? '').' '.($resp['errMsg'] ?? '')) ?: null;
                    $o->save();

                    $this->storeArcaLog($o, 'error',
                        ['tipo'=>$tipo,'pto'=>$pto,'nro'=>$nro,'monto'=>$monto,'ali'=>$ali,'cond'=>$cond,'docTipo'=>$docTipo,'docNro'=>$docNro],
                        ['resultado'=>$resp['resultado'] ?? null,'errCode'=>$resp['errCode'] ?? null,'errMsg'=>$resp['errMsg'] ?? null,'obsCode'=>$resp['obsCode'] ?? null,'obsMsg'=>$resp['obsMsg'] ?? null]
                    );
                    $err++;
                }
            } catch (\Throwable $e) {
                $o->arca_status = 'error'; $o->save();
                $this->storeArcaLog($o, 'error', null, null, $e->getMessage());
                $err++;
            }
        }

        return back()->with('status', "Facturación ejecutada. OK: {$ok} | Error: {$err}");
    }

    /** FACTURAR PACK (misma semántica que tenías, pero directo al servicio ARCA) */
    public function facturarPack(Request $request)
    {
        $packId = (string) $request->input('pack_id');
        if (!$packId) return back()->withErrors('Falta pack_id');

        $orders = MlibreOrder::with(['items','payments'])
            ->where('pack_id', $packId)
            ->orderBy('date_created')->get();

        if ($orders->isEmpty()) return back()->withErrors('Pack no encontrado');

        $eligible = $orders->every(function($o){
            return $o->status === 'paid'
                && !$o->invoiced
                && !(bool)($o->ml_invoice_attached ?? false)
                && !(bool)($o->ml_invoiced_by_ml ?? false);
        });
        if (!$eligible) return back()->withErrors('El pack tiene órdenes no elegibles');

        // Tomamos cabecera/límites del pack (primera orden)
        $first = $orders->first();

        try {
            [$docTipo, $docNro] = $this->mapDocToAfip($first->buyer_doc_type ?? null, $first->buyer_doc_number ?? null);
            $pto = (int)($request->input('pto', 7));
            $ali = (float)($request->input('ali', 21.0));
            $cond= (int)($request->input('cond', 5));

            // Monto total = suma de órdenes del pack
            $montoTotal = (float) $orders->sum(fn($o) => $o->total_amount ?? $o->paid_amount ?? 0);
            if ($montoTotal <= 0) return back()->withErrors('Pack con monto total inválido');

            // $tipo = ($docTipo === 80) ? 1 : 6;

            [$t,$s,$c] = app(ArcaWsaaHttpService::class)->loginCms();
            $wsfe      = app(ArcaWsfeHttpService::class);
            // Detectar condición del receptor y tipo de comprobante correcto
            [$condDetectado, $tipo] = $this->resolverCondYTipo($wsfe, $docTipo, $docNro);

            // Si querés permitir override manual desde el form, respetalo:
            $cond = (int) ($request->input('cond', $cond ?? $condDetectado));

            // Validación básica (RG 5616): si vamos a A, cond debe ser 1
            if ($tipo === 1 && $cond !== 1) {
                // Fallback automático a B si no es RI
                $tipo = 6;
            }

            $nro = $this->reservarNumeroComprobante($wsfe, [$t,$s,$c], $pto, $tipo);

            if ($tipo === 6) {
                $resp = $wsfe->solicitarCaeFacturaB([$t,$s,$c], $pto, $nro, $this->nf($montoTotal), $cond, $ali, $docTipo, (int)$docNro);
            } else {
                $neto = round($montoTotal / (1 + ($ali/100)), 2);
                $resp = $wsfe->solicitarCaeFacturaA([$t,$s,$c], $pto, $nro, $this->nf($neto), $ali, $docTipo, (int)$docNro, 1);
            }

            
            $okAprob = ($resp['resultado'] ?? null) === 'A';
            $cae     = $resp['cae'] ?? null;
            $vto     = $this->normalizarFechaVto($resp['vto'] ?? null);

            if ($okAprob && $cae) {
                foreach ($orders as $o) {
                    $o->invoiced       = true;
                    $o->arca_status    = 'success';
                    $o->invoice_type   = ($tipo === 1) ? 'A' : 'B';
                    $o->pos_number     = $pto;
                    $o->invoice_number = $nro; // misma numeración para todas en el pack (si tu criterio es uno-a-pack)
                    $o->invoice_date   = now()->toDateString();
                    $o->cae            = $cae;
                    $o->cae_due_date   = $vto ?: $o->cae_due_date;
                    $o->save();
                }

                $this->storeArcaLog($first, 'success',
                    ['tipo'=>$tipo,'pto'=>$pto,'nro'=>$nro,'monto'=>$montoTotal,'ali'=>$ali,'cond'=>$cond,'docTipo'=>$docTipo,'docNro'=>$docNro,'pack_id'=>$packId],
                    ['resultado'=>'A','cae'=>$cae,'vto'=>$vto]
                );

                try { $this->sincronizarEnML($first, null); } catch (\Throwable $e) {}

                // Confirmación estricta opcional
                if (config('arca.strict_confirm', env('ARCA_STRICT_CONFIRM', false))
                    && method_exists($wsfe, 'feCompConsultar')) {
                    try {
                        $conf = $wsfe->feCompConsultar([$t,$s,$c], $pto, $tipo, $nro);
                        if (($conf['resultado'] ?? '') !== 'A') {
                            foreach ($orders as $o) { $o->arca_status='warning'; $o->save(); }
                        }
                    } catch (\Throwable $e) {
                        Log::warning('ARCA strict confirm falló (pack)', ['pack'=>$packId,'msg'=>$e->getMessage()]);
                    }
                }

                return back()->with('status', 'Pack facturado correctamente.');
            }

            $this->storeArcaLog($first,'error',
                ['tipo'=>$tipo,'pto'=>$pto,'nro'=>$nro,'monto'=>$montoTotal,'ali'=>$ali,'cond'=>$cond,'docTipo'=>$docTipo,'docNro'=>$docNro,'pack_id'=>$packId],
                ['resultado'=>$resp['resultado'] ?? null,'errCode'=>$resp['errCode'] ?? null,'errMsg'=>$resp['errMsg'] ?? null,'obsCode'=>$resp['obsCode'] ?? null,'obsMsg'=>$resp['obsMsg'] ?? null]
            );
            return back()->withErrors('ARCA: No aprobado o sin CAE');

        } catch (\Throwable $e) {
            foreach ($orders as $o) { $o->arca_status = 'error'; $o->save(); }
            $this->storeArcaLog($orders->first(),'error',['pack_id'=>$packId], null, $e->getMessage());
            return back()->withErrors('Excepción emitiendo con ARCA: '.$e->getMessage());
        }
    }

    // ======================== Helpers ========================

    /** Reserva nro de comprobante con lock por (pto,tipo); si no existe numerador, consulta UltimoAutorizado */
    private function reservarNumeroComprobante(\App\Services\Arca\ArcaWsfeHttpService $wsfe, array $ta, int $pto, int $tipo): int
{
    // Lock a nivel MySQL para (pto,tipo) sin tabla extra
    $lockKey = "afip_nro_{$tipo}_{$pto}";

    // Intentamos tomar el lock por hasta 5 segundos
    $row = DB::selectOne('SELECT GET_LOCK(?, 5) AS l', [$lockKey]);
    $got = (isset($row->l) || isset($row->L)) ? ((int)($row->l ?? $row->L) === 1) : false;

    if (!$got) {
        // No se pudo tomar el lock: evitamos emitir a ciegas
        // (si preferís, podés seguir sin lock, pero es riesgoso en concurrencia)
        throw new \RuntimeException("No fue posible reservar numeración (lock {$lockKey}). Reintentá.");
    }

    try {
        // Pedimos a AFIP el último autorizado real y usamos el siguiente
        $ult = $wsfe->ultimoAutorizado($ta, $pto, $tipo);
        $next = ((int)$ult) + 1;

        // Devolvemos el próximo número a emitir
        return $next;
    } finally {
        // Liberamos siempre el lock, incluso si hay excepción en medio
        DB::select('SELECT RELEASE_LOCK(?)', [$lockKey]);
    }
}


    /** Normaliza AAAAMMDD → YYYY-MM-DD */
    private function normalizarFechaVto(?string $yyyymmdd): ?string
    {
        if (!$yyyymmdd || strlen($yyyymmdd)!==8) return null;
        return substr($yyyymmdd,0,4).'-'.substr($yyyymmdd,4,2).'-'.substr($yyyymmdd,6,2);
    }

    /** Redondeo AFIP estricto (2 decimales, punto) */
    private function nf(float $n): float
    {
        return (float) number_format($n, 2, '.', '');
    }

    /** Mapea doc del comprador a AFIP (mantengo tu lógica base) */
    private function mapDocToAfip(?string $tipo, ?string $numero): array
    {
        $n = preg_replace('/\D+/', '', (string)$numero);
        $map = ['CUIT'=>80,'CUIL'=>80,'DNI'=>96,'LE'=>89,'LC'=>90,'CI'=>87,'PAS'=>94];
        $docTipoAfip = $map[strtoupper((string)$tipo)] ?? 99;
        if ($docTipoAfip === 99) $n = '0';
        if ($docTipoAfip === 80 && strlen($n) !== 11) $docTipoAfip = 96; // si CUIT mal formado → DNI
        return [$docTipoAfip, (int)$n];
    }
/** =================== LOG ARCA =================== */
/**
 * Guarda un registro en arca_facturar_logs y devuelve el ID.
 *
 * @param \App\Models\MlibreOrder $o
 * @param 'success'|'error'|'warning'|'processing' $status
 * @param array|null $req   Datos relevantes del request (tipo, pto, nro, montos, doc, etc.)
 * @param array|null $resp  Resumen de respuesta (resultado, cae, vto, err/obs, raw opcional)
 * @param string|null $err  Mensaje de excepción si la hubo
 */
private function storeArcaLog($o, string $status, ?array $req, ?array $resp, ?string $err = null): int
{
    // Campos estándar (ajustá nombres si tu tabla tiene otros)
    $payload = [
        'mlibre_order_id' => $o->id ?? null,
        'status'          => $status,
        'request_json'    => $req ? json_encode($req, JSON_UNESCAPED_UNICODE) : null,

        // Si tu servicio WSFE devuelve XML o el SOAP crudo, dejalo en 'response_xml'
        'response_xml'    => $resp['raw'] ?? null,

        // Códigos/obs parseados del WSFE (si vienen)
        'error_code'      => $resp['errCode'] ?? null,
        'error_message'   => $resp['errMsg']  ?? $err,
        'obs_code'        => $resp['obsCode'] ?? null,
        'obs_message'     => $resp['obsMsg']  ?? null,

        // Timestamps
        'created_at'      => now(),
        'updated_at'      => now(),
    ];

    return (int) DB::table('arca_facturar_logs')->insertGetId($payload);
}

/** =================== NOTA EN ML =================== */
/**
 * Crea nota interna en la orden de Mercado Libre y persiste (opcional).
 * Respeta flags .env:
 *   ML_SYNC_NOTES=true/false
 *   ML_UPLOAD_INVOICE / ML_POSTSALE_MESSAGE (stubs)
 */
private function sincronizarEnML($order, ?string $pdfPath = null): void
{
    $syncNotes       = env('ML_SYNC_NOTES', true);
    $uploadInvoice   = env('ML_UPLOAD_INVOICE', false);
    $postSaleMessage = env('ML_POSTSALE_MESSAGE', false);

    if (!$syncNotes && !$uploadInvoice && !$postSaleMessage) {
        return;
    }

    $token = app(\App\Services\Mlibre\MlibreTokenService::class)->getValidAccessToken($order->seller_id);

    // A) Nota interna en la orden con datos de factura
    if ($syncNotes && $order->order_id && $order->cae) {
        $nota = $this->buildFacturaNote($order);
        try {
            $resp = Http::withToken($token)
                ->post("https://api.mercadolibre.com/orders/{$order->order_id}/notes", ['note' => $nota]);

            $ok   = $resp->successful();
            $body = $ok ? ($resp->json() ?? []) : null;

            // Persistir en ESTA orden
            $this->persistirNotaLocal($order, $nota, $body['id'] ?? null);

            // 🔁 Propagar pastilla a TODAS las órdenes del MISMO PACK (sin re-postear a ML)
            if ($ok && $order->pack_id) {
                DB::table('mlibre_orders')
                    ->where('pack_id', $order->pack_id)
                    ->update([
                        'ml_note_id'         => $body['id'] ?? null,
                        'ml_note_text'       => $nota,
                        'ml_note_posted_at'  => now(),
                        'updated_at'         => now(),
                    ]);
            }

            // Log visible en “Ver log”
            $this->storeArcaLog(
                $order,
                $ok ? 'success' : 'error',
                ['action'=>'ml_note','order_id'=>$order->order_id,'pack_id'=>$order->pack_id,'note'=>$nota],
                ['resultado'=>$ok ? 'OK' : ('HTTP '.$resp->status()), 'raw'=>$resp->body()],
                $ok ? null : 'Falló POST /orders/{id}/notes'
            );

        } catch (\Throwable $e) {
            $this->storeArcaLog($order, 'error', ['action'=>'ml_note','order_id'=>$order->order_id,'pack_id'=>$order->pack_id,'note'=>$nota], null, $e->getMessage());
            Log::warning('No se pudo crear nota ML', ['order_id'=>$order->order_id, 'err'=>$e->getMessage()]);
        }
    }

    // B) (Opcional) Mensaje post-venta con adjunto
    if ($postSaleMessage && $pdfPath && file_exists($pdfPath)) {
        try {
            $this->mlibreEnviarMensajeConAdjunto($order, $pdfPath, $token);
        } catch (\Throwable $e) {
            $this->storeArcaLog($order, 'error', ['action'=>'ml_message','order_id'=>$order->order_id], null, $e->getMessage());
        }
    }

    // C) (Opcional) Subir factura como invoice
    if ($uploadInvoice && $pdfPath && file_exists($pdfPath)) {
        try {
            $this->mlibreUploadInvoice($order->order_id, $pdfPath, $token);
        } catch (\Throwable $e) {
            $this->storeArcaLog($order, 'error', ['action'=>'ml_invoice','order_id'=>$order->order_id], null, $e->getMessage());
        }
    }
}

private function persistirNotaLocal($order, string $nota, ?string $noteId): void
{
    // Guardá lo que exista en tu schema (evita errores si faltan columnas)
    if (Schema::hasColumn('mlibre_orders', 'ml_note_id'))        $order->ml_note_id = $noteId;
    if (Schema::hasColumn('mlibre_orders', 'ml_note_text'))      $order->ml_note_text = $nota;
    if (Schema::hasColumn('mlibre_orders', 'ml_note_posted_at')) $order->ml_note_posted_at = now();
    $order->save();
}



private function buildFacturaNote($o): string
{
    // Detecta letra desde código (1/6/11) o desde texto
    $letra = $this->tipoLetra($o->invoice_type ?? null);
    if ($letra === null && property_exists($o, 'invoice_type_code')) {
        $letra = $this->tipoLetra($o->invoice_type_code ?? null);
    }
    if ($letra === null) $letra = 'B'; // fallback

    $num = str_pad((string)($o->invoice_number ?? ''), 8, '0', STR_PAD_LEFT);
    $vto = $o->cae_due_date ?? '';
    return "Factura {$letra} {$o->pos_number}-{$num} | CAE {$o->cae}" . ($vto ? " (vto {$vto})" : "");
}

private function tipoLetra($tipo): ?string
{
    if ($tipo === null) return null;

    // Si viene numérico (1/6/11)
    if (is_numeric($tipo)) {
        $t = (int)$tipo;
        return match ($t) {
            1 => 'A',
            6 => 'B',
            11 => 'C',
            default => 'B', // fallback razonable
        };
    }

    // Si viene como string ('A','B','C' o '1','6','11')
    $s = strtoupper((string)$tipo);
    if (in_array($s, ['A','B','C'], true)) return $s;

    if (ctype_digit($s)) {
        $t = (int)$s;
        return match ($t) {
            1 => 'A',
            6 => 'B',
            11 => 'C',
            default => 'B',
        };
    }

    return null;
}


/** Stubs (solo si más adelante activás estos flags) */
private function mlibreUploadInvoice($orderId, string $pdfPath, string $token): void { /* TODO */ }



private function mlibreEnviarMensajeConAdjunto($order, string $pdfPath, string $token): void
{
    // Si no tenemos pack_id o buyer_id, registramos log y salimos
    if (!$order->pack_id || !$order->buyer_id) {
        $this->storeArcaLog($order, 'warning', ['action'=>'ml_message','reason'=>'faltan pack_id/buyer_id'], null, null);
        return;
    }

    // TODO: Implementar endpoint real de mensajería de ML si tu cuenta/site lo permite.
    // Por ahora, registramos un log claro para que aparezca en “Ver log”.
    $this->storeArcaLog($order, 'info', [
        'action'    => 'ml_message',
        'pack_id'   => $order->pack_id,
        'buyer_id'  => $order->buyer_id,
        'pdf'       => basename($pdfPath),
        'status'    => 'pendiente-implementacion'
    ], null, null);
}
/**
 * Devuelve [$t,$s,$c] de forma tolerante:
 * 1) Usa cache por ~10 horas.
 * 2) Intenta ArcaWsfeHttpService::getOrRefreshTA() si existe.
 * 3) Fallback a ArcaWsaaHttpService::loginCms().
 * 4) Si WSAA devuelve coe.alreadyAuthenticated, re‑intenta getOrRefreshTA().
 */
private function obtenerTA($wsfe): array
{
    // 1) Cachea el TA para evitar WSAA repetido
    $cacheKey = 'ARCA_TA_'.config('arca.env', 'produccion');
    $ta = Cache::get($cacheKey);
    if (is_array($ta) && count($ta) === 3) {
        return $ta;
    }

    // 2) Si el servicio WSFE tiene getOrRefreshTA(), usarlo primero
    if (method_exists($wsfe, 'getOrRefreshTA')) {
        try {
            $ta = $wsfe->getOrRefreshTA();
            if (is_array($ta) && count($ta) === 3) {
                Cache::put($cacheKey, $ta, now()->addHours(10));
                return $ta;
            }
        } catch (\Throwable $e) {
            // seguimos al fallback
        }
    }

    // 3) Fallback: WSAA loginCms()
    try {
        [$t,$s,$c] = app(\App\Services\Arca\ArcaWsaaHttpService::class)->loginCms();
        $ta = [$t,$s,$c];
        Cache::put($cacheKey, $ta, now()->addHours(10));
        return $ta;
    } catch (\Throwable $e) {
        $msg = (string)$e->getMessage();

        // 4) Si el WSAA responde "alreadyAuthenticated", reintentar getOrRefreshTA()
        if (Str::contains($msg, ['alreadyAuthenticated','El CEE ya posee un TA'])) {
            if (method_exists($wsfe, 'getOrRefreshTA')) {
                $ta = $wsfe->getOrRefreshTA(); // debería devolver el TA vigente
                if (is_array($ta) && count($ta) === 3) {
                    Cache::put($cacheKey, $ta, now()->addHours(10));
                    return $ta;
                }
            }
            // Último recurso: si tenés un método para leer TA del disco, llamalo aquí.
            // e.g. if (method_exists($wsfe, 'getCachedTA')) { return $wsfe->getCachedTA(); }
        }

        // Si nada funcionó, relanzamos con mensaje claro
        throw new \RuntimeException('WSAA/TA no disponible: '.$msg);
    }
}
/**
 * Intenta obtener Condicion IVA del receptor y decide A/B:
 * - Si DocTipo=80 (CUIT) y el servicio expone FEParamGetCondicionIvaReceptor, lo usa.
 * - Si cond=1 (RI) ⇒ A (tipo=1). En cualquier otro caso ⇒ B (tipo=6).
 * - Si no se puede saber (sin método o error), usa fallback configurable:
 *     ARCA_FORCE_B_WHEN_UNKNOWN=true  ⇒ B con cond=5
 *     caso contrario ⇒ mantiene heurística por doc (CUIT⇒A, NO CUIT⇒B)
 *
 * @return array{int cond, int tipo}
 */
private function resolverCondYTipo($wsfe, int $docTipo, int $docNro): array
{
    // Valores por defecto
    $tipo = ($docTipo === 80) ? 1 : 6; // heurística inicial
    $cond = 5; // CF por defecto

    // Si no es CUIT, no intentamos lookup
    if ($docTipo !== 80) {
        return [$cond, 6];
    }

    // Intento de lookup si el servicio lo tiene
    $condSrv = null;
    try {
        // nombrados posibles para el método (ajusta al que tengas)
        foreach (['feParamGetCondicionIvaReceptor', 'getCondicionIvaReceptor', 'FEParamGetCondicionIvaReceptor'] as $m) {
            if (method_exists($wsfe, $m)) {
                $condSrv = (int) $wsfe->{$m}($docNro); // debería devolver código AFIP (1,4,5,6,...)
                break;
            }
        }
    } catch (\Throwable $e) {
        $condSrv = null;
    }

    if (is_int($condSrv) && $condSrv > 0) {
        $cond = $condSrv;
        // Solo A si cond=1 (RI). Caso contrario, B.
        $tipo = ($cond === 1) ? 1 : 6;
        return [$cond, $tipo];
    }

    // Sin lookup: decide por fallback
    if (env('ARCA_FORCE_B_WHEN_UNKNOWN', true)) {
        // Con CUIT pero sin saber condición ⇒ emitir B con CF (5) para evitar 10243
        return [5, 6];
    }

    // Heurística antigua (no recomendado):
    return [($docTipo === 80 ? 1 : 5), $tipo];
}

}
