# Guía KOI2 + AFIP (WSAA/WSFE)

## Tópico
Integración completa de KOI2 con **AFIP/ARCA** vía **WSAA** (autenticación) y **WSFE** (facturación electrónica), en **Homologación** y **Producción**.

## Objetivo
Dejar operativo el flujo end‑to‑end para emitir comprobantes **B** y **A** desde KOI2 (y **C** si el perfil impositivo aplica), con scripts de prueba, servicios Laravel robustos y un comando Artisan para uso diario.

---

## Hitos logrados (timeline)
1) **Ambiente & PHP**
- PHP 8.2 en Ubuntu 20.04. Se usan `ext-curl`, `ext-openssl`, `ext-simplexml` (no dependemos de `php-soap`).
- Se trabajó desde Tinker y luego con script CLI dedicado.

2) **Certificados**
- Se detectó mezcla de certificados **PROD** en la carpeta de **HOMO** → error `cms.cert.untrusted`.
- Se generó e instaló **certificado de Homologación** separado: 
  - Homo: `/var/www/koi2_v1/storage/arca/homo/{cert.crt,priv.key}`
  - Prod: `/var/www/koi2/storage/arca/prod/{cert.crt,priv.key}`
- Verificación `modulus` entre **cert** y **key** OK.

3) **WSAA**
- Al principio: `cms.bad.base64` por LTR inválido.
- Se generó **LTR** correcto y se firmó con `openssl cms -sign -outform DER -binary -nodetach -nosmimecap -noattr`.
- Se envió con **SOAPAction: "loginCms"** y headers correctos. Se parseó TA (token/sign) desde la respuesta SOAP.

4) **WSFE Dummy**
- En PROD, inicialmente `SSL dh key too small` ⇒ se forzó **TLS 1.2** y `DEFAULT@SECLEVEL=1`.
- Se usó HTTP/1.1, `Expect:` vacío, `Connection: close`, y **SOAPAction entre comillas**.
- Dummy **OK** en HOMO y PROD.

5) **FECompUltimoAutorizado**
- Se corrigió el uso de `$t,$s,$c` y se obtuvo el último número correctamente.

6) **FECAESolicitar**
- **Factura C (11)**: rechazo `10246` (falta **CondicionIVAReceptorId** por RG 5616). Se agregó el campo.
- En PROD: rechazo `10000` (perfil impositivo no monotributo/exento) y `10005` (PV debe ser **RECE**). Queda documentado: **C** sólo si CUIT y PV califican.
- **Factura B (6)**: éxito. Ejemplo reciente PROD PV **007** → **CAE 75335479119059**, vto **20250826**.
- **Factura A (1)**: se agregó método con DocTipo/DocNro reales y cálculo de IVA.

7) **Servicios Laravel estabilizados**
- Se reescribió `ArcaWsfeHttpService::call()` usando **cURL directo** (misma semántica que `curl(1)`) para evitar HTML 200 del WAF y asegurar TLS/headers.
- Métodos listos: `dummy()`, `ultimoAutorizado()`, `solicitarCaeFacturaC()`, `solicitarCaeFacturaB()`, **`solicitarCaeFacturaA()`** (nuevo).

8) **Script de prueba**
- `scripts/test_arca_cli.php` para HOMO/PROD con flujo WSAA→WSFE y armado de SOAP.
- Ejemplos ejecutados con éxito (B en PROD, C en HOMO).

9) **Comando Artisan**
- **`php artisan arca:facturar`**: emite A/B/C con opciones (pto, doc, cond IVA, alícuota, etc.).

---

## Instalaciones requeridas
- **Sistema**: `curl`, `openssl`.
- **PHP**: `ext-curl`, `ext-openssl`, `ext-simplexml` (Laravel ya incluido).
- (Opcional) `php8.2-soap` **no** es requerido porque vamos con HTTP+SOAP manual.

---

## Estructura de archivos
```
/var/www/koi2/                       # app prod
  └─ storage/arca/
       ├─ prod/
       │   ├─ cert.crt
       │   └─ priv.key
       └─ homo/
           ├─ cert.crt
           └─ priv.key

/var/www/koi2/scripts/test_arca_cli.php
```

---

## Configuración KOI2 (`config/arca.php`)
```php
return [
  'env'    => env('ARCA_ENV', 'produccion'), // homologacion | produccion
  'cuit'   => env('ARCA_CUIT'),
  'cert'   => env('ARCA_CERT'),
  'key'    => env('ARCA_KEY'),
  'key_pass' => env('ARCA_KEY_PASS', ''),
  'service'  => 'wsfe',
  'wsaa' => [
    'homologacion' => [
      'endpoint' => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms',
      'wsdl'     => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms?WSDL',
      'action'   => 'loginCms',
    ],
    'produccion' => [
      'endpoint' => 'https://wsaa.afip.gov.ar/ws/services/LoginCms',
      'wsdl'     => 'https://wsaa.afip.gov.ar/ws/services/LoginCms?WSDL',
      'action'   => 'loginCms',
    ],
  ],
  'wsfe' => [
    'homologacion' => [
      'endpoint'  => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
      'wsdl'      => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL',
      'namespace' => 'http://ar.gov.afip.dif.FEV1/',
    ],
    'produccion' => [
      'endpoint'  => 'https://servicios1.afip.gov.ar/wsfev1/service.asmx',
      'wsdl'      => 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL',
      'namespace' => 'http://ar.gov.afip.dif.FEV1/',
    ],
  ],
];
```

### `.env` (producción en `/var/www/koi2/`)
```
ARCA_ENV=produccion
ARCA_CUIT=30716182815
ARCA_CERT=/var/www/koi2/storage/arca/prod/cert.crt
ARCA_KEY=/var/www/koi2/storage/arca/prod/priv.key
ARCA_KEY_PASS=
```

### `.env` (homologación en `/var/www/koi2_v1/` o similar)
```
ARCA_ENV=homologacion
ARCA_CUIT=30716182815
ARCA_CERT=/var/www/koi2_v1/storage/arca/homo/cert.crt
ARCA_KEY=/var/www/koi2_v1/storage/arca/homo/priv.key
ARCA_KEY_PASS=
```

---

## Certificado: chequeos y conversión
- Ver asunto y emisor:
```bash
openssl x509 -in /ruta/cert.crt -noout -subject -issuer -dates
```
- Validar par cert/key:
```bash
openssl x509 -noout -modulus -in cert.crt | openssl md5
openssl rsa  -noout -modulus -in priv.key | openssl md5
```
- Si bajaste `.cer` (DER) y necesitás `.crt` (PEM):
```bash
openssl x509 -inform DER -in cert_homo.cer -out cert.crt
```

Permisos recomendados:
```bash
chown -R www-data:www-data /var/www/koi2/storage/arca
chmod 700 /var/www/koi2/storage/arca
chmod 600 /var/www/koi2/storage/arca/*/priv.key
chmod 640 /var/www/koi2/storage/arca/*/cert.crt
```

---

## WSAA: LTR + CMS + login
Generar LTR y firmar (DER + base64):
```bash
LUID=$(date +%s)
GEN=$(date -u -d "-5 min" +"%Y-%m-%dT%H:%M:%SZ")
EXP=$(date -u -d "+10 min" +"%Y-%m-%dT%H:%M:%SZ")
cat > /tmp/ltr.xml <<EOF
<?xml version="1.0" encoding="UTF-8"?>
<loginTicketRequest version="1.0">
  <header>
    <uniqueId>$LUID</uniqueId>
    <generationTime>$GEN</generationTime>
    <expirationTime>$EXP</expirationTime>
  </header>
  <service>wsfe</service>
</loginTicketRequest>
EOF

openssl cms -sign -in /tmp/ltr.xml \
  -signer /var/www/koi2/storage/arca/prod/cert.crt \
  -inkey  /var/www/koi2/storage/arca/prod/priv.key \
  -out /tmp/cms.der -outform DER -binary -nodetach -nosmimecap -noattr
CMSB64=$(base64 /tmp/cms.der | tr -d '
')
```

**Nota**: En código Laravel ya se automatiza (servicio WSAA).

---

## Pruebas rápidas con `curl` (WSFE Dummy)
```bash
curl -4 --http1.1 --tlsv1.2 \
  --ciphers 'DEFAULT@SECLEVEL=1' \
  -H 'Content-Type: text/xml; charset=utf-8' \
  -H 'SOAPAction: "http://ar.gov.afip.dif.FEV1/FEDummy"' \
  -H 'Expect:' \
  --data '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><FEDummy xmlns="http://ar.gov.afip.dif.FEV1/" /></soapenv:Body></soapenv:Envelope>' \
  https://servicios1.afip.gov.ar/wsfev1/service.asmx
```

---

## Script de prueba (CLI)
Ubicación: `/var/www/koi2/scripts/test_arca_cli.php`

Ejemplos:
```bash
# HOMO – Factura C
php /var/www/koi2_v1/scripts/test_arca_cli.php homo 30716182815 1 11 1000.00 5

# PROD – Factura B (total 121.00, CF, PV 007)
php /var/www/koi2/scripts/test_arca_cli.php prod 30716182815 7 6 121.00 5
```

Resultado real de PROD (B):
```
WSAA OK: token/sign recibidos
WSFE dummy HTTP: 200
UltimoAutorizado: 1494 → Proximo: 1495
Resultado: A
CAE: 75335479119059  Vto: 20250826
```

---

## Servicios Laravel
- `ArcaWsfeHttpService::call()` usa cURL: IPv4, TLS 1.2, `DEFAULT@SECLEVEL=1`, `SOAPAction` **entre comillas**, `Accept: text/xml`, `Expect:` vacío, `Connection: close`, `Host` explícito.
- Métodos disponibles:
  - `dummy()` → `['AppServer'=>'OK', ...]`
  - `ultimoAutorizado([$t,$s,$c], $pto, $tipo)`
  - `solicitarCaeFacturaC([$t,$s,$c], $pto, $nro, $total)` (sólo si CUIT/PV habilitado)
  - `solicitarCaeFacturaB([$t,$s,$c], $pto, $nro, $totalConIva, $condIVA=5, $ali=21)`
  - **`solicitarCaeFacturaA([$t,$s,$c], $pto, $nro, $neto, $ali=21, $docTipo=80, $docNro, $condIVA=1)`**

## Comando Artisan
Registrar en `Kernel` y usar:
```bash
# Factura A (RI, CUIT receptor, neto 100 a 21%)
php artisan arca:facturar 1 100.00 --pto=7 --docTipo=80 --docNro=20XXXXXXXXX --cond=1 --ali=21

# Factura B (CF, total 121)
php artisan arca:facturar 6 121.00 --pto=7 --cond=5 --ali=21

# Factura C (si corresponde por régimen/PV)
php artisan arca:facturar 11 1000.00 --pto=7 --cond=5
```

---

## Punto de Venta 007
- En **AFIP** debe existir PV **007** dado de alta como **RECE** y habilitado para **WS**.
- Para **C**, además el CUIT debe ser **Mono** o **Exento**; si no, errores 10000/10005.

---

## Errores comunes (y solución)
- `cms.bad.base64` → LTR mal formado o CMS no DER/base64.
- `cms.cert.untrusted` → Cert de PROD usado en HOMO (o viceversa). Usar la AC correcta.
- `coe.notAuthorized` (WSAA) → El certificado no está autorizado al servicio. Habilitarlo en AFIP y volver a descargar/instalar.
- `SSL routines: dh key too small` → Forzar TLS 1.2 + `DEFAULT@SECLEVEL=1`.
- **HTML 200** con número aleatorio → WAF/Proxy. Usar headers y cURL como en `call()`.
- `10246` → Falta `CondicionIVAReceptorId` (RG 5616).
- `10000`/`10005` → Perfil/PV no habilitado para el tipo de comprobante.

---

## Próximos pasos
- Cache de **TA** (token/sign) con vencimiento (p.ej. 10–12 hs) para evitar logins repetidos.
- Log de request/response (con máscara de datos sensibles) y trazas de errores.
- Encolar emisión y reintento suave ante fallas transitorias.
- Validaciones de negocio: ICA/perc., documentos receptores, totales y redondeos.

---

## Resumen
- **HOMO** funcionando (C, pruebas). **PROD** funcionando (B; A disponible con datos reales del receptor). 
- Servicios Laravel sólidos + **CLI** y **Artisan** listos. 
- PV **007** operando en PROD.
