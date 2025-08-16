<?php
/**
 * Configuración ARCA (ex-AFIP) para KOI2_v1
 *
 * Usa variables de .env. No cierres con "?>".
 * Entornos válidos: 'homologacion' | 'produccion'
 */

return [

    // Ambiente y CUIT
    'env'      => env('ARCA_ENV', 'homologacion'),
    'cuit'     => env('ARCA_CUIT'),

    // Rutas a certificado/clave (PEM/CRT y KEY)
    'cert'     => env('ARCA_CERT_PATH'),
    'key'      => env('ARCA_KEY_PATH'),
    'key_pass' => env('ARCA_KEY_PASS', ''), // si tu KEY tiene passphrase, ponela en .env

    // Servicio: 'wsfe' (facturación electrónica) o 'wsmtxca' (ítems detallados)
    'service'  => env('ARCA_SERVICE', 'wsfe'),

    // WSAA (autenticación)
    'wsaa' => [
        'homologacion' => [
            // Para SoapClient
            'wsdl'     => env('ARCA_WSAA_HOMO_WSDL', 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms?WSDL'),
            // Para llamadas HTTP/RAW SOAP
            'endpoint' => env('ARCA_WSAA_HOMO_ENDPOINT', 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms'),
            'action'   => 'loginCms',
        ],
        'produccion' => [
            'wsdl'     => env('ARCA_WSAA_PROD_WSDL', 'https://wsaa.afip.gov.ar/ws/services/LoginCms?WSDL'),
            'endpoint' => env('ARCA_WSAA_PROD_ENDPOINT', 'https://wsaa.afip.gov.ar/ws/services/LoginCms'),
            'action'   => 'loginCms',
        ],
    ],

    // WSFEv1 (facturación)
    'wsfe' => [
        'homologacion' => [
            'wsdl'      => env('ARCA_WSFE_HOMO_WSDL', 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL'),
            'endpoint'  => env('ARCA_WSFE_HOMO_ENDPOINT', 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx'),
            'namespace' => 'http://ar.gov.afip.dif.FEV1/',
        ],
        'produccion' => [
            'wsdl'      => env('ARCA_WSFE_PROD_WSDL', 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL'),
            'endpoint'  => env('ARCA_WSFE_PROD_ENDPOINT', 'https://servicios1.afip.gov.ar/wsfev1/service.asmx'),
            'namespace' => 'http://ar.gov.afip.dif.FEV1/',
        ],
    ],

];
