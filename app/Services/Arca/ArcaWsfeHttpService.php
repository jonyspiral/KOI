<?php

namespace App\Services\Arca;

use RuntimeException;

class ArcaWsfeHttpService
{
    private function endpoint(): string
    {
        $env = config('arca.env', 'homologacion');           // 'produccion' | 'homologacion'
        return (string) config("arca.wsfe.{$env}.endpoint"); // e.g. https://servicios1.afip.gov.ar/wsfev1/service.asmx
    }

    /**
     * Llama a WSFE (ASMX) con:
     * - HTTP/1.1 + IPv4
     * - TLS 1.2 y DEFAULT@SECLEVEL=1 (evita "dh key too small")
     * - SOAPAction CON COMILLAS (requisito de .asmx)
     */
    private function call(string $action, string $bodyXml): \SimpleXMLElement
    {
        $endpoint = $this->endpoint();
        $host     = parse_url($endpoint, PHP_URL_HOST) ?: 'servicios1.afip.gov.ar';

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

        // Headers tipo curl(1) que demostraron funcionar
        $headers = [
            'Host: ' . $host,
            'User-Agent: curl/7.68.0',
            'Content-Type: text/xml; charset=utf-8',
            'Accept: text/xml',
            'SOAPAction: "http://ar.gov.afip.dif.FEV1/' . $action . '"', // ¡con comillas!
            'Expect:',            // evita 100-continue
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
            CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2, // TLS 1.2
        ]);

        $raw  = \curl_exec($ch);
        $err  = \curl_error($ch);
        $code = (int) \curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        \curl_close($ch);

        if ($raw === false) {
            throw new RuntimeException("WSFE cURL error: {$err}");
        }

        // Respuesta no-200: muchas veces es SOAP Fault (XML). Intentamos parsear igual.
        if ($code !== 200) {
            try {
                return new \SimpleXMLElement($raw);
            } catch (\Throwable $e) {
                $snippet = mb_substr(preg_replace('/\s+/', ' ', $raw), 0, 300);
                throw new RuntimeException("WSFE HTTP {$code}: contenido no XML. Snippet: {$snippet}");
            }
        }

        // 200 OK: parseo directo (si el WAF devolviera HTML, esto explotaría y mostramos snippet claro)
        try {
            return new \SimpleXMLElement($raw);
        } catch (\Throwable $e) {
            $snippet = mb_substr(preg_replace('/\s+/', ' ', $raw), 0, 300);
            throw new RuntimeException("WSFE 200 pero no XML/SOAP válido. Snippet: {$snippet}");
        }
    }

    /** Prueba de salud del servicio */
    public function dummy(): array
    {
        $xml  = $this->call('FEDummy', '<FEDummy xmlns="http://ar.gov.afip.dif.FEV1/" />');

        $body = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;
        $res  = $body->children('http://ar.gov.afip.dif.FEV1/')->FEDummyResponse->FEDummyResult;

        return [
            'AppServer'  => (string) $res->AppServer,
            'DbServer'   => (string) $res->DbServer,
            'AuthServer' => (string) $res->AuthServer,
        ];
    }

    /** Último número autorizado para (PtoVta, CbteTipo) */
    public function ultimoAutorizado(array $ta, int $ptoVta, int $cbteTipo): int
    {
        [$token, $sign, $cuit] = $ta;

        $body = <<<XML
<FECompUltimoAutorizado xmlns="http://ar.gov.afip.dif.FEV1/">
  <Auth><Token>{$token}</Token><Sign>{$sign}</Sign><Cuit>{$cuit}</Cuit></Auth>
  <PtoVta>{$ptoVta}</PtoVta>
  <CbteTipo>{$cbteTipo}</CbteTipo>
</FECompUltimoAutorizado>
XML;

        $xml = $this->call('FECompUltimoAutorizado', $body);

        $res = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body
                   ->children('http://ar.gov.afip.dif.FEV1/')->FECompUltimoAutorizadoResponse
                   ->FECompUltimoAutorizadoResult;

        return (int) $res->CbteNro;
    }

    /**
     * Solicita CAE para Factura C (CbteTipo=11).
     * Incluye CondicionIVAReceptorId (RG 5616).
     */
    public function solicitarCaeFacturaC(
        array $ta,
        int $ptoVta,
        int $nro,
        float $impTotal,
        int $condIvaReceptorId = 5,   // CF por defecto
        int $docTipo = 99,
        int $docNro  = 0
    ): array {
        [$token, $sign, $cuit] = $ta;
        $hoy = (new \DateTime())->format('Ymd');

        $imp = number_format($impTotal, 2, '.', '');

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
        <ImpTotal>{$imp}</ImpTotal>
        <ImpTotConc>0.00</ImpTotConc>
        <ImpNeto>{$imp}</ImpNeto>
        <ImpOpEx>0.00</ImpOpEx>
        <ImpIVA>0.00</ImpIVA>
        <ImpTrib>0.00</ImpTrib>
        <MonId>PES</MonId>
        <MonCotiz>1.000000</MonCotiz>
        <CondicionIVAReceptorId>{$condIvaReceptorId}</CondicionIVAReceptorId>
      </FECAEDetRequest>
    </FeDetReq>
  </FeCAEReq>
</FECAESolicitar>
XML;

        $xml   = $this->call('FECAESolicitar', $req);
        $body  = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;
        $fe    = $body->children('http://ar.gov.afip.dif.FEV1/')->FECAESolicitarResponse->FECAESolicitarResult;
        $det   = $fe->FeDetResp->FECAEDetResponse[0];

        $resultado = (string) ($det->Resultado ?: $fe->FeCabResp->Resultado);
        $cae       = (string) $det->CAE;
        $vto       = (string) $det->CAEFchVto;

        $errCode = $errMsg = $obsCode = $obsMsg = null;
        if (isset($fe->Errors)) {
            $e = $fe->Errors->Err[0] ?? $fe->Errors->Err;
            $errCode = (string) $e->Code;
            $errMsg  = (string) $e->Msg;
        }
        if (isset($det->Observaciones)) {
            $o = $det->Observaciones->Obs[0] ?? $det->Observaciones->Obs;
            $obsCode = (string) $o->Code;
            $obsMsg  = (string) $o->Msg;
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
     * $totalConIva: total con IVA incluido (ej. 121.00)
     * $condIvaReceptorId: CF=5 (RI=1, Exento=2, Monotributo=3, etc.)
     * $alicuotaPct: 21.0 por defecto (map: 21→Id=5, 10.5→4, 27→6, 5→8, 2.5→9, 0→3)
     */
    public function solicitarCaeFacturaB(
        array $ta,
        int $ptoVta,
        int $nro,
        float $totalConIva,
        int $condIvaReceptorId = 5,
        float $alicuotaPct = 21.0,
        int $docTipo = 99,
        int $docNro  = 0
    ): array {
        [$token, $sign, $cuit] = $ta;
        $hoy  = (new \DateTime())->format('Ymd');

        // Base + IVA desde total
        $base = round($totalConIva / (1.0 + $alicuotaPct/100.0), 2);
        $iva  = round($totalConIva - $base, 2);

        // Evita "Implicit conversion from float … to int": usamos claves string
        $pctKey = rtrim(rtrim(number_format($alicuotaPct, 3, '.', ''), '0'), '.'); // 21.0 -> "21", 10.5 -> "10.5"
        $map = [
            '21'   => 5,
            '10.5' => 4,
            '27'   => 6,
            '5'    => 8,
            '2.5'  => 9,
            '0'    => 3,
        ];
        $idAlic = $map[$pctKey] ?? 5;

        $fmtTotal = number_format($totalConIva, 2, '.', '');
        $fmtBase  = number_format($base,        2, '.', '');
        $fmtIva   = number_format($iva,         2, '.', '');

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

        $resultado = (string) ($det->Resultado ?: $fe->FeCabResp->Resultado);
        $cae       = (string) $det->CAE;
        $vto       = (string) $det->CAEFchVto;

        $errCode = $errMsg = $obsCode = $obsMsg = null;
        if (isset($fe->Errors)) {
            $e = $fe->Errors->Err[0] ?? $fe->Errors->Err;
            $errCode = (string) $e->Code;
            $errMsg  = (string) $e->Msg;
        }
        if (isset($det->Observaciones)) {
            $o = $det->Observaciones->Obs[0] ?? $det->Observaciones->Obs;
            $obsCode = (string) $o->Code;
            $obsMsg  = (string) $o->Msg;
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