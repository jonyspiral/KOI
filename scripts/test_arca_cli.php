#!/usr/bin/env php
<?php
/**
 * Test ARCA/AFIP WSAA+WSFE (HOMO/PROD) - KOI2
 * Uso:
 *   php test_arca_cli.php  homo|prod  CUIT  PTO  TIPO  IMPORTE  CondIVAReceptorId
 * Ejemplos:
 *   Factura C (11) CF:
 *     php test_arca_cli.php homo 30716182815 1 11 1000.00 5
 *   Factura B (6) CF total 121.00 (21% incluido):
 *     php test_arca_cli.php prod 30716182815 7 6 121.00 5
 */

if (PHP_SAPI !== 'cli') { fwrite(STDERR, "Run from CLI\n"); exit(1); }

function xmlNS(SimpleXMLElement $xml): array { return $xml->getNamespaces(true); }

/** POST SOAP con headers correctos (ASMX exige SOAPAction ENTRE COMILLAS) + TLS 1.2 + seclevel=1 */
function postSoap(string $url, string $soapAction, string $xml): array {
  $ch = curl_init($url);
  $headers = [
    'Content-Type: text/xml; charset=utf-8',
    'Accept: text/xml',
    'SOAPAction: "'.$soapAction.'"', // <- IMPORTANTE: con comillas
    'Expect:',                       // evita 100-continue
    'Connection: close',
  ];
  curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_POSTFIELDS     => $xml,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_SSL_CIPHER_LIST=> 'DEFAULT@SECLEVEL=1',
    CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2,
  ]);
  $body = curl_exec($ch);
  $err  = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  curl_close($ch);
  return [$code, $body, $err];
}

function wsaaLogin(string $env, string $cert, string $key): array {
  // LTR
  $ltr = sprintf(
    '<?xml version="1.0" encoding="UTF-8"?>'.
    '<loginTicketRequest version="1.0">'.
      '<header>'.
        '<uniqueId>%d</uniqueId>'.
        '<generationTime>%s</generationTime>'.
        '<expirationTime>%s</expirationTime>'.
      '</header>'.
      '<service>wsfe</service>'.
    '</loginTicketRequest>',
    time(), gmdate('Y-m-d\TH:i:s\Z', time()-600), gmdate('Y-m-d\TH:i:s\Z', time()+1200)
  );
  $ltrFile = '/tmp/ltr.xml';
  file_put_contents($ltrFile, $ltr);

  // CMS DER
  $cmsDer = '/tmp/cms.der';
  $cmd = sprintf('/usr/bin/openssl cms -sign -in %s -signer %s -inkey %s -out %s -outform DER -binary -nodetach -nosmimecap -noattr 2>/dev/null',
                 escapeshellarg($ltrFile), escapeshellarg($cert), escapeshellarg($key), escapeshellarg($cmsDer));
  exec($cmd, $o, $rc);
  if ($rc !== 0 || !is_file($cmsDer)) { throw new RuntimeException("OpenSSL CMS failed"); }
  $cmsB64 = rtrim(str_replace("\n", '', base64_encode(file_get_contents($cmsDer))));
  printf("CMS (base64) len: %d\n", strlen($cmsB64));

  // Endpoints WSAA
  $WSAA = $env === 'prod'
    ? 'https://wsaa.afip.gov.ar/ws/services/LoginCms'
    : 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms';

  // Sobre SOAP
  $envSoap = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.logincms/">
  <soapenv:Header/>
  <soapenv:Body>
    <ar:loginCms><ar:in0>{$cmsB64}</ar:in0></ar:loginCms>
  </soapenv:Body>
</soapenv:Envelope>
XML;

  [$code,$body,$err] = postSoap($WSAA, 'loginCms', $envSoap);
  if ($code !== 200) throw new RuntimeException("WSAA HTTP $code: \n$body");

  $xml = new SimpleXMLElement($body); $ns = xmlNS($xml);
  $b   = $xml->children($ns['soapenv'] ?? $ns['soap'] ?? 'http://schemas.xmlsoap.org/soap/envelope/')->Body;
  $ret = $b->xpath('.//*[local-name()="loginCmsReturn"]');
  if (!$ret || !isset($ret[0])) throw new RuntimeException("WSAA parse error");
  $ta  = new SimpleXMLElement((string)$ret[0]);
  $t   = (string)$ta->credentials->token;
  $s   = (string)$ta->credentials->sign;
  if (!$t || !$s) throw new RuntimeException("WSAA empty token/sign");
  echo "WSAA OK: token/sign recibidos\n";
  return [$t,$s];
}

function wsfeDummy(string $wsfeUrl): void {
  $env = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.FEV1/"><soap:Header/><soap:Body><ar:FEDummy/></soap:Body></soap:Envelope>';
  [$code,$body,$err] = postSoap($wsfeUrl, 'http://ar.gov.afip.dif.FEV1/FEDummy', $env);
  echo "WSFE dummy HTTP: $code\n";
  if ($code !== 200) { throw new RuntimeException("WSFE Dummy HTTP $code: $body"); }
}

function wsfeUltimoAutorizado(string $wsfeUrl, string $t, string $s, string $cuit, int $pto, int $tipo): int {
  $env = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.FEV1/">'.
           '<soap:Header/>'.
           '<soap:Body>'.
             '<ar:FECompUltimoAutorizado>'.
               '<ar:Auth><ar:Token>'.$t.'</ar:Token><ar:Sign>'.$s.'</ar:Sign><ar:Cuit>'.$cuit.'</ar:Cuit></ar:Auth>'.
               '<ar:PtoVta>'.$pto.'</ar:PtoVta><ar:CbteTipo>'.$tipo.'</ar:CbteTipo>'.
             '</ar:FECompUltimoAutorizado>'.
           '</soap:Body>'.
         '</soap:Envelope>';
  [$code,$body,$err] = postSoap($wsfeUrl, 'http://ar.gov.afip.dif.FEV1/FECompUltimoAutorizado', $env);
  if ($code !== 200) throw new RuntimeException("WSFE UltimoAutorizado HTTP $code: \n$body");
  $xml = new SimpleXMLElement($body); $ns = xmlNS($xml);
  $b   = $xml->children($ns['soap'] ?? $ns['soapenv'] ?? 'http://schemas.xmlsoap.org/soap/envelope/')->Body;
  $r   = $b->children('http://ar.gov.afip.dif.FEV1/')->FECompUltimoAutorizadoResponse->FECompUltimoAutorizadoResult;
  return (int)$r->CbteNro;
}

function wsfeSolicitarCAE_C(string $wsfeUrl, string $t, string $s, string $cuit, int $pto, int $tipo, int $nro, float $impTotal, int $condIvaId): array {
  $hoy = date('Ymd');
  $det = '<ar:FECAEDetRequest>'.
           '<ar:Concepto>1</ar:Concepto>'.
           '<ar:DocTipo>99</ar:DocTipo><ar:DocNro>0</ar:DocNro>'.
           '<ar:CbteDesde>'.$nro.'</ar:CbteDesde><ar:CbteHasta>'.$nro.'</ar:CbteHasta>'.
           '<ar:CbteFch>'.$hoy.'</ar:CbteFch>'.
           '<ar:ImpTotal>'.number_format($impTotal,2,'.','').'</ar:ImpTotal>'.
           '<ar:ImpTotConc>0.00</ar:ImpTotConc>'.
           '<ar:ImpNeto>'.number_format($impTotal,2,'.','').'</ar:ImpNeto>'.
           '<ar:ImpOpEx>0.00</ar:ImpOpEx>'.
           '<ar:ImpIVA>0.00</ar:ImpIVA>'.
           '<ar:MonId>PES</ar:MonId><ar:MonCotiz>1.000000</ar:MonCotiz>'.
           '<ar:CondicionIVAReceptorId>'.$condIvaId.'</ar:CondicionIVAReceptorId>'.
         '</ar:FECAEDetRequest>';

  $env = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.FEV1/"><soap:Header/><soap:Body>'.
           '<ar:FECAESolicitar>'.
             '<ar:Auth><ar:Token>'.$t.'</ar:Token><ar:Sign>'.$s.'</ar:Sign><ar:Cuit>'.$cuit.'</ar:Cuit></ar:Auth>'.
             '<ar:FeCAEReq>'.
               '<ar:FeCabReq><ar:CantReg>1</ar:CantReg><ar:PtoVta>'.$pto.'</ar:PtoVta><ar:CbteTipo>'.$tipo.'</ar:CbteTipo></ar:FeCabReq>'.
               '<ar:FeDetReq>'.$det.'</ar:FeDetReq>'.
             '</ar:FeCAEReq>'.
           '</ar:FECAESolicitar>'.
         '</soap:Body></soap:Envelope>';

  [$code,$body,$err] = postSoap($wsfeUrl, 'http://ar.gov.afip.dif.FEV1/FECAESolicitar', $env);
  if ($code !== 200) throw new RuntimeException("WSFE FECAESolicitar HTTP $code: \n$body");
  return parseFeResult($body);
}

function wsfeSolicitarCAE_B(string $wsfeUrl, string $t, string $s, string $cuit, int $pto, int $tipo, int $nro, float $total_con_iva, int $condIvaId, float $aliPerc = 21.0): array {
  $base = round($total_con_iva / (1.0 + $aliPerc/100.0), 2);
  $iva  = round($total_con_iva - $base, 2);
  $hoy  = date('Ymd');
  $idAlic = 5; // 21%

  $det = '<ar:FECAEDetRequest>'.
           '<ar:Concepto>1</ar:Concepto>'.
           '<ar:DocTipo>99</ar:DocTipo><ar:DocNro>0</ar:DocNro>'.
           '<ar:CbteDesde>'.$nro.'</ar:CbteDesde><ar:CbteHasta>'.$nro.'</ar:CbteHasta>'.
           '<ar:CbteFch>'.$hoy.'</ar:CbteFch>'.
           '<ar:ImpTotal>'.number_format($total_con_iva,2,'.','').'</ar:ImpTotal>'.
           '<ar:ImpTotConc>0.00</ar:ImpTotConc>'.
           '<ar:ImpNeto>'.number_format($base,2,'.','').'</ar:ImpNeto>'.
           '<ar:ImpOpEx>0.00</ar:ImpOpEx>'.
           '<ar:ImpIVA>'.number_format($iva,2,'.','').'</ar:ImpIVA>'.
           '<ar:ImpTrib>0.00</ar:ImpTrib>'.
           '<ar:MonId>PES</ar:MonId><ar:MonCotiz>1.000000</ar:MonCotiz>'.
           '<ar:CondicionIVAReceptorId>'.$condIvaId.'</ar:CondicionIVAReceptorId>'.
           '<ar:Iva>'.
             '<ar:AlicIva>'.
               '<ar:Id>'.$idAlic.'</ar:Id>'.
               '<ar:BaseImp>'.number_format($base,2,'.','').'</ar:BaseImp>'.
               '<ar:Importe>'.number_format($iva,2,'.','').'</ar:Importe>'.
             '</ar:AlicIva>'.
           '</ar:Iva>'.
         '</ar:FECAEDetRequest>';

  $env = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.FEV1/"><soap:Header/><soap:Body>'.
           '<ar:FECAESolicitar>'.
             '<ar:Auth><ar:Token>'.$t.'</ar:Token><ar:Sign>'.$s.'</ar:Sign><ar:Cuit>'.$cuit.'</ar:Cuit></ar:Auth>'.
             '<ar:FeCAEReq>'.
               '<ar:FeCabReq><ar:CantReg>1</ar:CantReg><ar:PtoVta>'.$pto.'</ar:PtoVta><ar:CbteTipo>'.$tipo.'</ar:CbteTipo></ar:FeCabReq>'.
               '<ar:FeDetReq>'.$det.'</ar:FeDetReq>'.
             '</ar:FeCAEReq>'.
           '</ar:FECAESolicitar>'.
         '</soap:Body></soap:Envelope>';

  [$code,$body,$err] = postSoap($wsfeUrl, 'http://ar.gov.afip.dif.FEV1/FECAESolicitar', $env);
  if ($code !== 200) throw new RuntimeException("WSFE FECAESolicitar HTTP $code: \n$body");
  return parseFeResult($body);
}

function parseFeResult(string $body): array {
  $xml = new SimpleXMLElement($body); $ns = xmlNS($xml);
  $b   = $xml->children($ns['soap'] ?? $ns['soapenv'] ?? 'http://schemas.xmlsoap.org/soap/envelope/')->Body;
  $fe  = $b->children('http://ar.gov.afip.dif.FEV1/')->FECAESolicitarResponse->FECAESolicitarResult;
  $det = $fe->FeDetResp->FECAEDetResponse[0];

  $CAE = (string)$det->CAE;
  $Vto = (string)$det->CAEFchVto;
  $Res = (string)($det->Resultado ?: $fe->FeCabResp->Resultado);

  $errCode = $errMsg = $obsCode = $obsMsg = null;
  if (isset($fe->Errors)) {
    $e = $fe->Errors->Err[0] ?? $fe->Errors->Err;
    $errCode = (string)$e->Code; $errMsg = (string)$e->Msg;
  }
  if (isset($det->Observaciones)) {
    $o = $det->Observaciones->Obs[0] ?? $det->Observaciones->Obs;
    $obsCode = (string)$o->Code; $obsMsg = (string)$o->Msg;
  }
  return compact('CAE','Vto','Res','errCode','errMsg','obsCode','obsMsg','body');
}

/* ===================== MAIN ===================== */
if ($argc < 7) {
  fwrite(STDERR, "Uso: php ".$argv[0]." homo|prod CUIT PTO TIPO IMPORTE CondIVAReceptorId\n");
  exit(2);
}
$ENV   = strtolower($argv[1]) === 'prod' ? 'prod' : 'homo';
$CUIT  = $argv[2];
$PTO   = (int)$argv[3];
$TIPO  = (int)$argv[4];
$IMP   = (float)$argv[5];
$CIVA  = (int)$argv[6];

$BASE  = ($ENV === 'prod') ? '/var/www/koi2/storage/arca/prod' : '/var/www/koi2/storage/arca/homo';
$CERT  = $BASE.'/cert.crt';
$KEY   = $BASE.'/priv.key';
$WSFE  = ($ENV === 'prod')
          ? 'https://servicios1.afip.gov.ar/wsfev1/service.asmx'
          : 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx';

echo "== ARCA prueba ($ENV) ==\n";

[$T,$S] = wsaaLogin($ENV, $CERT, $KEY);

wsfeDummy($WSFE);

$ult = wsfeUltimoAutorizado($WSFE, $T, $S, $CUIT, $PTO, $TIPO);
$next = $ult + 1;
echo "UltimoAutorizado: $ult \xE2\x86\x92 Proximo: $next\n";

if ($TIPO === 11) {
  $res = wsfeSolicitarCAE_C($WSFE, $T, $S, $CUIT, $PTO, $TIPO, $next, $IMP, $CIVA);
} elseif ($TIPO === 6) {
  $res = wsfeSolicitarCAE_B($WSFE, $T, $S, $CUIT, $PTO, $TIPO, $next, $IMP, $CIVA, 21.0);
} else {
  throw new RuntimeException('CbteTipo no soportado: '.$TIPO.' (usa 11=C o 6=B)');
}

echo "Resultado: ".$res['Res']."\n";
if (!empty($res['CAE'])) {
  echo "CAE: ".$res['CAE']."  Vto: ".$res['Vto']."\n";
} else {
  if (!empty($res['errCode']) || !empty($res['obsCode'])) {
    echo "Error: [".($res['errCode']??'-')."] ".($res['errMsg']??'')."\n";
    echo "Obs:   [".($res['obsCode']??'-')."] ".($res['obsMsg']??'')."\n";
  }
}
