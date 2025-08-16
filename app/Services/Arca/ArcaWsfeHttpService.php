<?php

namespace App\Services\Arca;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use SimpleXMLElement;

class ArcaWsfeHttpService
{
private function endpoint(): string
{
    $env = config('arca.env', 'homologacion');
    return config("arca.wsfe.{$env}.endpoint");
    
}

    /**
     * POST SOAP a WSFE con:
     *  - TLS 1.2 y DEFAULT@SECLEVEL=1 (evita "dh key too small")
     *  - SOAPAction ENTRE COMILLAS (requisito .asmx)
     *  - Headers Accept/Expect/Connection apropiados
     */
  private function call(string $action, string $bodyXml): \SimpleXMLElement
{
    $endpoint = $this->endpoint();
    $host     = parse_url($endpoint, PHP_URL_HOST);

    $envelope = <<<XML
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xmlns:xsd="http://www.w3.org/2001/XMLSchema"
               xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header/>
  <soap:Body>
    {$bodyXml}
  </soap:Body>
</soap:Envelope>
XML;

    $headers = [
        'Host: '.$host,
        'User-Agent: curl/7.68.0',
        'Content-Type: text/xml; charset=utf-8',
        'Accept: text/xml',
        'SOAPAction: "http://ar.gov.afip.dif.FEV1/'.$action.'"', // ASMX exige comillas
        'Expect:',                 // evita 100-continue
        'Connection: close',
    ];

    $ch = \curl_init($endpoint);
    \curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $envelope,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,       // fuerza IPv4
        CURLOPT_SSL_CIPHER_LIST=> 'DEFAULT@SECLEVEL=1',    // evita "dh key too small"
        CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2, // fuerza TLS 1.2
    ]);

    $raw  = \curl_exec($ch);
    $err  = \curl_error($ch);
    $code = (int)\curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    \curl_close($ch);

    if ($raw === false) {
        throw new \RuntimeException("WSFE cURL error: ".$err);
    }

    // Si NO es 200, intento parsear SOAP Fault para dar mensaje útil
    if ($code !== 200) {
        try {
            $xmlErr = new \SimpleXMLElement($raw);
            $ns     = $xmlErr->getNamespaces(true);
            $fault  = $xmlErr->children($ns['soap'] ?? $ns['soapenv'] ?? 'http://schemas.xmlsoap.org/soap/envelope/')
                             ->Body?->Fault ?? null;
            if ($fault) {
                $faultStr = (string)($fault->faultstring ?? '');
                throw new \RuntimeException("WSFE HTTP {$code}: {$faultStr}");
            }
        } catch (\Throwable $e) {
            // no era XML; caigo al throw genérico con snippet
        }
        throw new \RuntimeException("WSFE HTTP {$code}: ".mb_substr(preg_replace('/\s+/', ' ', $raw), 0, 600));
    }

    // 200 OK: intento parsear SIEMPRE (sin heurística de '<Envelope')
    try {
        return new \SimpleXMLElement($raw);
    } catch (\Throwable $e) {
        throw new \RuntimeException(
            "WSFE devolvió contenido no XML (HTTP {$code}). ".
            "Snippet: ".mb_substr(preg_replace('/\s+/', ' ', $raw), 0, 300)
        );
    }
}




    public function dummy(): array
    {
        $xml  = $this->call('FEDummy', '<FEDummy xmlns="http://ar.gov.afip.dif.FEV1/" />');
        $body = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;
        $res  = $body->children('http://ar.gov.afip.dif.FEV1/')->FEDummyResponse->FEDummyResult;

        return [
            'AppServer' => (string)$res->AppServer,
            'DbServer'  => (string)$res->DbServer,
            'AuthServer'=> (string)$res->AuthServer,
        ];
    }

    public function ultimoAutorizado(array $ta, int $ptoVta, int $cbteTipo): int
    {
        [$token,$sign,$cuit] = $ta;
        $body = <<<XML
<FECompUltimoAutorizado xmlns="http://ar.gov.afip.dif.FEV1/">
  <Auth><Token>{$token}</Token><Sign>{$sign}</Sign><Cuit>{$cuit}</Cuit></Auth>
  <PtoVta>{$ptoVta}</PtoVta>
  <CbteTipo>{$cbteTipo}</CbteTipo>
</FECompUltimoAutorizado>
XML;

        $xml = $this->call('FECompUltimoAutorizado', $body);
        $res = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body
                   ->children('http://ar.gov.afip.dif.FEV1/')->FECompUltimoAutorizadoResponse->FECompUltimoAutorizadoResult;

        return (int)$res->CbteNro;
    }

    /**
     * Solicita CAE para Factura C (CbteTipo=11).
     * Devuelve array con campos útiles y el XML raw (debug).
     */
    public function solicitarCaeFacturaC(
        array $ta, int $ptoVta, int $nro, float $impTotal, int $docTipo = 99, int $docNro = 0
    ): array {
        [$token,$sign,$cuit] = $ta;
        $hoy = (new \DateTime())->format('Ymd');

        $req = <<<XML
<FECAESolicitar xmlns="http://ar.gov.afip.dif.FEV1/">
  <Auth><Token>{$token}</Token><Sign>{$sign}</Sign><Cuit>{$cuit}</Cuit></Auth>
  <FeCAEReq>
    <FeCabReq><CantReg>1</CantReg><PtoVta>{$ptoVta}</PtoVta><CbteTipo>11</CbteTipo></FeCabReq>
    <FeDetReq>
      <FECAEDetRequest>
        <Concepto>1</Concepto>
        <DocTipo>{$docTipo}</DocTipo>
        <DocNro>{$docNro}</DocNro>
        <CbteDesde>{$nro}</CbteDesde>
        <CbteHasta>{$nro}</CbteHasta>
        <CbteFch>{$hoy}</CbteFch>
        <ImpTotal>{$impTotal}</ImpTotal>
        <ImpTotConc>0.00</ImpTotConc>
        <ImpNeto>{$impTotal}</ImpNeto>
        <ImpOpEx>0.00</ImpOpEx>
        <ImpIVA>0.00</ImpIVA>
        <ImpTrib>0.00</ImpTrib>
        <MonId>PES</MonId>
        <MonCotiz>1.000000</MonCotiz>
      </FECAEDetRequest>
    </FeDetReq>
  </FeCAEReq>
</FECAESolicitar>
XML;

        $xml = $this->call('FECAESolicitar', $req);

        $body   = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;
        $fe     = $body->children('http://ar.gov.afip.dif.FEV1/')->FECAESolicitarResponse->FECAESolicitarResult;
        $det    = $fe->FeDetResp->FECAEDetResponse[0];

        $resultado = (string)($det->Resultado ?: $fe->FeCabResp->Resultado);
        $cae       = (string)$det->CAE;
        $vto       = (string)$det->CAEFchVto;

        // errores/obs (si vienen)
        $errCode = $errMsg = $obsCode = $obsMsg = null;
        if (isset($fe->Errors)) {
            $e = $fe->Errors->Err[0] ?? $fe->Errors->Err;
            $errCode = (string)$e->Code; $errMsg = (string)$e->Msg;
        }
        if (isset($det->Observaciones)) {
            $o = $det->Observaciones->Obs[0] ?? $det->Observaciones->Obs;
            $obsCode = (string)$o->Code; $obsMsg = (string)$o->Msg;
        }

        return [
            'resultado' => $resultado,
            'cae'       => $cae,
            'vto'       => $vto,
            'cab'       => $fe->FeCabResp ?? null,
            'det'       => $det ?? null,
            'errCode'   => $errCode,
            'errMsg'    => $errMsg,
            'obsCode'   => $obsCode,
            'obsMsg'    => $obsMsg,
            'raw'       => $xml->asXML(), // para debug
        ];
    }
    public function solicitarCaeFacturaA(
    array $ta,
    int $ptoVta,
    int $nro,
    float $neto,                   // base imponible sin IVA
    float $alicuotaPct = 21.0,
    int $docTipo = 80,             // 80=CUIT (lo usual para A)
    string|int $docNro = '',       // CUIT del receptor (sin guiones)
    int $condIvaReceptorId = 1     // 1 = Responsable Inscripto
): array {
    [$token,$sign,$cuit] = $ta;
    $hoy  = (new \DateTime())->format('Ymd');

    // Calcular IVA y total
    $iva   = round($neto * ($alicuotaPct/100.0), 2);
    $total = round($neto + $iva, 2);

    // Map % → Id alícuota AFIP
    $map = [
        '21.0' => 5,
        '10.5' => 4,
        '27.0' => 6,
        '5.0'  => 8,
        '2.5'  => 9,
        '0.0'  => 3,
    ];
    $key    = number_format((float)$alicuotaPct, 1, '.', '');
    $idAlic = $map[$key] ?? 5;

    $fmtTotal = number_format($total, 2, '.', '');
    $fmtBase  = number_format($neto,  2, '.', '');
    $fmtIva   = number_format($iva,   2, '.', '');

    $req = <<<XML
<FECAESolicitar xmlns="http://ar.gov.afip.dif.FEV1/">
  <Auth><Token>{$token}</Token><Sign>{$sign}</Sign><Cuit>{$cuit}</Cuit></Auth>
  <FeCAEReq>
    <FeCabReq>
      <CantReg>1</CantReg>
      <PtoVta>{$ptoVta}</PtoVta>
      <CbteTipo>1</CbteTipo>
    </FeCabReq>
    <FeDetReq>
      <FECAEDetRequest>
        <Concepto>1</Concepto>
        <DocTipo>{$docTipo}</DocTipo>
        <DocNro>{$docNro}</DocNro>
        <CbteDesde>{$nro}</CbteDesde>
        <CbteHasta>{$nro}</CbteHasta>
        <CbteFch>{$hoy}</CbteFch>

        <ImpTotal>{$fmtTotal}</ImpTotal>
        <ImpTotConc>0.00</ImpTotConc>
        <ImpNeto>{$fmtBase}</ImpNeto>
        <ImpOpEx>0.00</ImpOpEx>
        <ImpIVA>{$fmtIva}</ImpIVA>
        <ImpTrib>0.00</ImpTrib>

        <MonId>PES</MonId>
        <MonCotiz>1.000000</MonCotiz>

        <CondicionIVAReceptorId>{$condIvaReceptorId}</CondicionIVAReceptorId>

        <Iva>
          <AlicIva>
            <Id>{$idAlic}</Id>
            <BaseImp>{$fmtBase}</BaseImp>
            <Importe>{$fmtIva}</Importe>
          </AlicIva>
        </Iva>
      </FECAEDetRequest>
    </FeDetReq>
  </FeCAEReq>
</FECAESolicitar>
XML;

    $xml  = $this->call('FECAESolicitar', $req);

    $body = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;
    $fe   = $body->children('http://ar.gov.afip.dif.FEV1/')->FECAESolicitarResponse->FECAESolicitarResult;
    $det  = $fe->FeDetResp->FECAEDetResponse[0];

    $resultado = (string)($det->Resultado ?: $fe->FeCabResp->Resultado);
    $cae       = (string)$det->CAE;
    $vto       = (string)$det->CAEFchVto;

    $errCode = $errMsg = $obsCode = $obsMsg = null;
    if (isset($fe->Errors)) {
        $e = $fe->Errors->Err[0] ?? $fe->Errors->Err;
        $errCode = (string)$e->Code; $errMsg = (string)$e->Msg;
    }
    if (isset($det->Observaciones)) {
        $o = $det->Observaciones->Obs[0] ?? $det->Observaciones->Obs;
        $obsCode = (string)$o->Code; $obsMsg = (string)$o->Msg;
    }

    return [
        'resultado' => $resultado,
        'cae'       => $cae,
        'vto'       => $vto,
        'cab'       => $fe->FeCabResp ?? null,
        'det'       => $det ?? null,
        'errCode'   => $errCode,
        'errMsg'    => $errMsg,
        'obsCode'   => $obsCode,
        'obsMsg'    => $obsMsg,
        'raw'       => $xml->asXML(),
    ];
}

    /**
 * Solicita CAE para Factura B (CbteTipo=6) a Consumidor Final (por defecto).
 * - $totalConIva: total con IVA incluido (p.ej. 121.00)
 * - $condIvaReceptorId: CF=5 (otros: RI=1, Exento=2, Monotributo=3, etc.)
 * - $alicuotaPct: 21.0 por defecto (map: 21→Id=5, 10.5→4, 27→6, 5→8, 2.5→9, 0→3)
 */
public function solicitarCaeFacturaB(
    array $ta,
    int $ptoVta,
    int $nro,
    float $totalConIva,
    int $condIvaReceptorId = 5,
    float $alicuotaPct = 21.0,
    int $docTipo = 99,
    int $docNro = 0
): array {
    [$token,$sign,$cuit] = $ta;
    $hoy  = (new \DateTime())->format('Ymd');

    // Calcular base + IVA desde total con IVA
    $base = round($totalConIva / (1.0 + $alicuotaPct/100.0), 2);
    $iva  = round($totalConIva - $base, 2);

    // Map de porcentaje → Id de alícuota AFIP
    $map = [
        21.0 => 5,
        10.5 => 4,
        27.0 => 6,
        5.0  => 8,
        2.5  => 9,
        0.0  => 3,
    ];
    $idAlic = $map[$alicuotaPct] ?? 5;

    $req = <<<XML
<FECAESolicitar xmlns="http://ar.gov.afip.dif.FEV1/">
  <Auth><Token>{$token}</Token><Sign>{$sign}</Sign><Cuit>{$cuit}</Cuit></Auth>
  <FeCAEReq>
    <FeCabReq>
      <CantReg>1</CantReg>
      <PtoVta>{$ptoVta}</PtoVta>
      <CbteTipo>6</CbteTipo>
    </FeCabReq>
    <FeDetReq>
      <FECAEDetRequest>
        <Concepto>1</Concepto>
        <DocTipo>{$docTipo}</DocTipo>
        <DocNro>{$docNro}</DocNro>
        <CbteDesde>{$nro}</CbteDesde>
        <CbteHasta>{$nro}</CbteHasta>
        <CbteFch>{$hoy}</CbteFch>

        <ImpTotal>#{TOTAL}</ImpTotal>
        <ImpTotConc>0.00</ImpTotConc>
        <ImpNeto>#{BASE}</ImpNeto>
        <ImpOpEx>0.00</ImpOpEx>
        <ImpIVA>#{IVA}</ImpIVA>
        <ImpTrib>0.00</ImpTrib>

        <MonId>PES</MonId>
        <MonCotiz>1.000000</MonCotiz>

        <CondicionIVAReceptorId>{$condIvaReceptorId}</CondicionIVAReceptorId>

        <Iva>
          <AlicIva>
            <Id>{$idAlic}</Id>
            <BaseImp>#{BASE}</BaseImp>
            <Importe>#{IVA}</Importe>
          </AlicIva>
        </Iva>
      </FECAEDetRequest>
    </FeDetReq>
  </FeCAEReq>
</FECAESolicitar>
XML;

    // Formateo con 2 decimales y punto
    $fmtTotal = number_format($totalConIva, 2, '.', '');
    $fmtBase  = number_format($base,        2, '.', '');
    $fmtIva   = number_format($iva,         2, '.', '');
    $req = strtr($req, [
        '#{TOTAL}' => $fmtTotal,
        '#{BASE}'  => $fmtBase,
        '#{IVA}'   => $fmtIva,
    ]);

    $xml  = $this->call('FECAESolicitar', $req);

    $body = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;
    $fe   = $body->children('http://ar.gov.afip.dif.FEV1/')->FECAESolicitarResponse->FECAESolicitarResult;
    $det  = $fe->FeDetResp->FECAEDetResponse[0];

    $resultado = (string)($det->Resultado ?: $fe->FeCabResp->Resultado);
    $cae       = (string)$det->CAE;
    $vto       = (string)$det->CAEFchVto;

    $errCode = $errMsg = $obsCode = $obsMsg = null;
    if (isset($fe->Errors)) {
        $e = $fe->Errors->Err[0] ?? $fe->Errors->Err;
        $errCode = (string)$e->Code; $errMsg = (string)$e->Msg;
    }
    if (isset($det->Observaciones)) {
        $o = $det->Observaciones->Obs[0] ?? $det->Observaciones->Obs;
        $obsCode = (string)$o->Code; $obsMsg = (string)$o->Msg;
    }

    return [
        'resultado' => $resultado,
        'cae'       => $cae,
        'vto'       => $vto,
        'cab'       => $fe->FeCabResp ?? null,
        'det'       => $det ?? null,
        'errCode'   => $errCode,
        'errMsg'    => $errMsg,
        'obsCode'   => $obsCode,
        'obsMsg'    => $obsMsg,
        'raw'       => $xml->asXML(),
    ];
}

}
