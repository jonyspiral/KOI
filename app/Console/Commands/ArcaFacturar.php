<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Arca\ArcaWsaaHttpService;
use App\Services\Arca\ArcaWsfeHttpService;

class ArcaFacturar extends Command
{
    protected $signature = 'arca:facturar
        {tipo : 1=A, 6=B, 11=C}
        {monto : Importe (A: neto; B: total c/IVA; C: total)}
        {--pto=7 : Punto de venta}
        {--docTipo=99 : DocTipo receptor (A: 80 CUIT, B/C: 99 por defecto)}
        {--docNro=0 : DocNro receptor}
        {--cond=5 : Condición IVA del receptor (RI=1, Exento=2, Mono=3, CF=5)}
        {--ali=21 : Alícuota IVA para A/B (%, ej 21)}
        {--nro= : Nro de comprobante (si no se pasa, consulta ultimo+1)}
    ';

    protected $description = 'Emite comprobante AFIP (A/B/C) vía WSFE usando configuración arca.*';

    public function handle(ArcaWsaaHttpService $wsaa, ArcaWsfeHttpService $wsfe)
    {
        [$t,$s,$cuit] = $wsaa->loginCms();

        $pto    = (int)$this->option('pto');
        $tipo   = (int)$this->argument('tipo');
        $monto  = (float)$this->argument('monto');
        $docT   = (int)$this->option('docTipo');
        $docN   = (string)$this->option('docNro');
        $cond   = (int)$this->option('cond');
        $ali    = (float)$this->option('ali');

        // nro
        $nroOpt = $this->option('nro');
        if ($nroOpt !== null && $nroOpt !== '') {
            $nro = (int)$nroOpt;
        } else {
            $ult = $wsfe->ultimoAutorizado([$t,$s,$cuit], $pto, $tipo);
            $nro = $ult + 1;
        }

        $this->info("Emitiendo tipo={$tipo}, pto={$pto}, nro={$nro}");

        if ($tipo === 6) {
            // B — monto = total con IVA
            $res = $wsfe->solicitarCaeFacturaB([$t,$s,$cuit], $pto, $nro, $monto, $cond, $ali, $docT, $docN);
        } elseif ($tipo === 11) {
            // C — monto = total (sin IVA)
            $res = $wsfe->solicitarCaeFacturaC([$t,$s,$cuit], $pto, $nro, $monto, $docT, (int)$docN);
        } elseif ($tipo === 1) {
            // A — monto = neto (sin IVA)
            if ($docT === 99 || !$docN) {
                $this->error('Para Factura A se requiere DocTipo/DocNro reales. Ej: --docTipo=80 --docNro=CUIT');
                return 1;
            }
            $res = $wsfe->solicitarCaeFacturaA([$t,$s,$cuit], $pto, $nro, $monto, $ali, $docT, $docN, $cond ?: 1);
        } else {
            $this->error("CbteTipo no soportado: {$tipo} (usa 1=A, 6=B, 11=C)");
            return 1;
        }

        $this->line('Resultado: '.$res['resultado']);
        if (!empty($res['cae'])) {
            $this->info('CAE: '.$res['cae'].'  Vto: '.$res['vto']);
        } else {
            if (!empty($res['errCode']) || !empty($res['obsCode'])) {
                $this->warn('Error: ['.($res['errCode']??'-').'] '.($res['errMsg']??''));
                $this->warn('Obs:   ['.($res['obsCode']??'-').'] '.($res['obsMsg']??''));
            }
        }
        return 0;
    }
}
