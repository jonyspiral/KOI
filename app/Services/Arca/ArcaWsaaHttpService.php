<?php

namespace App\Services\Arca;

use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Http;

use RuntimeException;
use SimpleXMLElement;

class ArcaWsaaHttpService
{
    public function loginCms(): array
{
    $conf   = config('arca');
    $env    = $conf['env'] ?? 'homologacion';
    $wsaa   = $conf['wsaa'][$env] ?? null;
    if (!$wsaa) {
        throw new RuntimeException("Config WSAA inválida para env: $env");
    }

    $endpoint = $wsaa['endpoint'] ?? '';
    $cert     = $conf['cert'] ?? '';
    $key      = $conf['key'] ?? '';
    $pass     = $conf['key_pass'] ?? '';
    $cuit     = $conf['cuit'] ?? null;
    $service  = $conf['service'] ?? 'wsfe';

    if (!$cert || !$key || !is_file($cert) || !is_file($key)) {
        throw new RuntimeException("Cert/Key no encontrados: {$cert} | {$key}");
    }

    // 1) LTR (UTC): ventana -5 / +10 min
    $uid = time();
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $gen = (clone $now)->sub(new DateInterval('PT5M'))->format('c');   // 2025-08-15T20:23:21+00:00
    $exp = (clone $now)->add(new DateInterval('PT10M'))->format('c');

    $ltr = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<loginTicketRequest version="1.0">
  <header>
    <uniqueId>{$uid}</uniqueId>
    <generationTime>{$gen}</generationTime>
    <expirationTime>{$exp}</expirationTime>
  </header>
  <service>{$service}</service>
</loginTicketRequest>
XML;

    // 2) Firmar a CMS DER (sin S/MIME) y base64 continuo
    $cmsB64 = $this->signToCmsDerBase64($ltr, $cert, $key, $pass);

    // 3) Sobre SOAP loginCms
    $soapBody = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.logincms/">
  <soapenv:Header/>
  <soapenv:Body>
    <ar:loginCms><ar:in0>{$cmsB64}</ar:in0></ar:loginCms>
  </soapenv:Body>
</soapenv:Envelope>
XML;

    // 4) HTTP/TLS para AFIP (.asmx): TLS1.2 + seclevel=1 + SOAPAction entre comillas
    $http = \Illuminate\Support\Facades\Http::withOptions([
        'curl' => [
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            CURLOPT_SSLVERSION      => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        ],
    ])->withHeaders([
        'Content-Type' => 'text/xml; charset=utf-8',
        'Accept'       => 'text/xml',
        'SOAPAction'   => '"loginCms"', // ← ASMX lo quiere entre comillas
        'Expect'       => '',           // evita 100-continue
        'Connection'   => 'close',
    ])->timeout(25);

    $resp = $http->withBody($soapBody, 'text/xml; charset=utf-8')->post($endpoint);

    // 5) Manejo de errores HTTP + Fault SOAP legible
    if (!$resp->ok()) {
        $bodyStr = $resp->body();
        try {
            $xmlErr = new SimpleXMLElement($bodyStr);
            $nsErr  = $xmlErr->getNamespaces(true);
            $bErr   = $xmlErr->children($nsErr['soapenv'] ?? $nsErr['soap'] ?? '')->Body ?? null;
            $fault  = $bErr?->Fault ?? null;
            if ($fault) {
                $faultStr = (string)($fault->faultstring ?? '');
                throw new RuntimeException("WSAA HTTP error: {$resp->status()} $faultStr");
            }
        } catch (\Throwable $e) {
            // si no es XML o no tiene Fault, tiro el cuerpo bruto
        }
        throw new RuntimeException("WSAA HTTP error: {$resp->status()} {$resp->body()}");
    }

    // 6) Parse TA
    $xml  = new SimpleXMLElement($resp->body());
    $ns   = $xml->getNamespaces(true);
    $body = $xml->children($ns['soapenv'] ?? $ns['soap'] ?? '')->Body ?? null;
    if (!$body) {
        throw new RuntimeException("SOAP Body no encontrado");
    }

    $ret = $body->xpath('.//*[local-name()="loginCmsReturn"]');
    if (!$ret || !isset($ret[0])) {
        throw new RuntimeException("loginCmsReturn no encontrado");
    }

    $taXml = (string)$ret[0];
    $ta    = new SimpleXMLElement($taXml);

    $token = (string)($ta->credentials->token ?? '');
    $sign  = (string)($ta->credentials->sign ?? '');

    if (!$token || !$sign) {
        throw new RuntimeException("WSAA no devolvió Token/Sign válidos");
    }

    return [$token, $sign, $cuit];
}

    private function signToCmsDerBase64(string $ltrXml, string $certPath, string $keyPath, string $pass = ''): string
    {
        $tmpDir = sys_get_temp_dir();
        $ltrFile = tempnam($tmpDir, 'ltr_');
        $cmsDer  = tempnam($tmpDir, 'cms_');
        file_put_contents($ltrFile, $ltrXml);

        // Fallback robusto: CLI "openssl cms -sign" a DER
        $cmd = sprintf(
            'openssl cms -sign -in %s -signer %s -inkey %s -out %s -outform DER -nosmimecap -binary -nodetach 2>&1',
            escapeshellarg($ltrFile),
            escapeshellarg($certPath),
            escapeshellarg($keyPath),
            escapeshellarg($cmsDer)
        );
        if ($pass !== '') {
            $pf = tempnam($tmpDir, 'pw_');
            file_put_contents($pf, $pass);
            $cmd = sprintf(
                'openssl cms -sign -in %s -signer %s -inkey %s -passin file:%s -out %s -outform DER -nosmimecap -binary -nodetach 2>&1',
                escapeshellarg($ltrFile),
                escapeshellarg($certPath),
                escapeshellarg($keyPath),
                escapeshellarg($pf),
                escapeshellarg($cmsDer)
            );
        }

        $out = [];
        $ret = 0;
        @exec($cmd, $out, $ret);
        if ($ret !== 0 || !is_file($cmsDer) || filesize($cmsDer) === 0) {
            @unlink($ltrFile); @unlink($cmsDer);
            throw new RuntimeException("Falla al firmar CMS (openssl cms): " . implode("\n", $out));
        }

        $bin = file_get_contents($cmsDer);
        @unlink($ltrFile); @unlink($cmsDer);
        if ($bin === false || strlen($bin) === 0) {
            throw new RuntimeException("CMS DER vacío");
        }

        return base64_encode($bin);
    }
}
