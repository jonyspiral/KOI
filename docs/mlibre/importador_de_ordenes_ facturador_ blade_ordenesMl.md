# Importador de Órdenes **Pagas** — Canvas Guía (Mercado Libre)

## 🎯 Objetivo

Dejar operativo un **importador de órdenes pagas** que **persiste en base de datos** (no solo CSV) y un **panel en Blade** para gestionar la facturación:

- Listar órdenes con **estado de envío** y **estado de facturación**.
- **Botón “Facturar”** por fila (activo solo si **no** está facturada).
- **Facturar seleccionadas** en lote.
- Registrar **logs** por cada intento de emisión con **ARCA/AFIP**.

---

## ✅ Resultado (estado actual)

> 🆕 **Novedades (17-08-2025):**
>
> - Nota interna en ML tras facturar (HTTP 201) con persistencia en DB: `ml_note_id`, `ml_note_posted_at`, `ml_note_text` e idempotencia por texto.
> - **ID de orden** enlazado al detalle ML (configurable con `ML_ORDER_URL`; por defecto `https://www.mercadolibre.com.ar/ventas/%s/detalle`).
> - Normalización de **Vto CAE** a `YYYY-MM-DD` cuando viene como `AAAAMMDD`.
> - Parser ARCA ampliado: captura `Emitiendo tipo=.., pto=.., nro=..` para persistir **PV** y **Nro**.
> - Confirmación AFIP **tolerante**: si no existe `feCompConsultar`, no marca *warning* (`ARCA_STRICT_CONFIRM=false`).

- Comando **Artisan**: `mlibre:importar-ordenes {desde} {hasta} [--estado=paid]`.
- **Persistencia en DB** (idempotente):
  - Cabecera: `mlibre_orders` (con campos de facturación ARCA: `invoiced`, `arca_status`, `cae`, etc.).
  - Renglones: `mlibre_order_items`.
  - Pagos: `mlibre_order_payments`.
  - Envío: `mlibre_shipments`.
  - Auditoría: `mlibre_orders_raw` (payload original).
- **UI Blade**: `/mlibre/orders` muestra columnas clave (cliente, doc, total, envío) y **estado de facturación**; incluye **Facturar** y **Facturar seleccionadas**.
- **Logs de ARCA**: `arca_facturar_logs` con request/response y errores.
- (Opcional) Exportar CSV sigue disponible pero **ya no es el camino principal**.

---

## 🔩 Flujo técnico (resumen)

1. **Token**: `getValidAccessToken(448490530)` → garantiza un `access_token` vigente para el vendedor correcto.
2. **Listado**: llamar a `/orders/search` con paginado (`limit=50`, `offset` incremental) y **parámetros recomendados** (abajo) para traer únicamente el universo a facturar.
3. **Detalle**: por cada `order_id`, llamar a `/orders/{id}` y componer los campos finales para el CSV:
   - `buyer.billing_info.doc_number` || `buyer.identification.number` → **documento**.
   - `order_items[].item.title` (+ `variation_attributes`) → **descripción**.
   - `total_amount` || `paid_amount` || *suma de pagos* → **monto**.
   - `payments[0].payment_type` || `payment_type_id` → **medio de pago**.
   - `shipping.receiver_address.address_line` → **dirección**, con **fallback** a `/shipments/{id}`.
4. **CSV**: se serializa y guarda en `storage/app/`.

---

## 🧰 Parámetros recomendados para `/orders/search`

- **Rango de fechas**:
  - `order.date_created.from=YYYY-MM-DDT00:00:00.000-03:00`
  - `order.date_created.to=YYYY-MM-DDT23:59:59.000-03:00`
- **Estado**:
  - `order.status=paid` *(solo ventas pagadas → facturación)*
- **Ordenamiento**:
  - `sort=date_desc` *(recientes primero)*
- **Paginado**:
  - `limit=50`, `offset=0,50,100,...`
- **Vendedor** (anclar al token correcto):
  - Opción A: **sin** `seller` (el token ya ancla al caller)
  - Opción B: `seller=448490530` (debe **coincidir** con el token)

> **Importante**: Evitar `seller=me` si hay riesgo de desalineación con el token; y si se usa `seller=448490530`, debe coincidir con el `caller.id` del token.

---

## 🧪 Checklist de verificación rápida

- **Token correcto**:
  - `getValidAccessToken(448490530)`
  - `GET /users/me` → debe devolver `id=448490530`.
- **Permisos (scope de ventas)**:
  - Si `/orders/search` devuelve 403 `PolicyAgent` o `caller.id...`, **reautorizar** la app y aceptar **permisos de Ventas/Órdenes** con la cuenta `SPIRALSHOES AR`.
- **User-Agent**:
  - Incluir `User-Agent: KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)` para evitar `Invalid app_version`.

---

## 🧯 Focos de conflicto & resoluciones

1. **403 **``

   - **Causa**: desalineación entre el **token** y el **vendedor** consultado; o falta de permisos de ventas.
   - **Fix**: usar token de `448490530` y **anclar** la consulta al mismo vendedor (sin `seller` o `seller=448490530`). Si persiste, **reautorizar** la app con scope de ventas.

2. **403 **``** (PolicyAgent)**

   - **Causa**: token inválido/expirado, revocado o app sin permisos.
   - **Fix**: refrescar token (OAuth refresh) o **reautorizar** con la cuenta del vendedor; verificar `GET /users/me`.

3. ``** en **``

   - **Causa**: en algunas órdenes, el título viene en `order_items[].item.title` (no en `order_items[].title`).
   - **Fix**: mapear con fallback a `item.title` y anexar `variation_attributes` si existen. También se robusteció monto, pagos y dirección con fallbacks y `/shipments/{id}`.

---

## 🧑‍💻 Bloques clave (referencia)

**Consulta paginada a **`` (pseudo):

```php
Http::withHeaders([$authHeaders])->get('https://api.mercadolibre.com/orders/search', [
  'order.date_created.from' => "$desdeT00:00:00.000-03:00",
  'order.date_created.to'   => "$hastaT23:59:59.000-03:00",
  'order.status'            => 'paid',      // recomendado
  'sort'                    => 'date_desc',
  'limit'                   => 50,
  'offset'                  => $offset,
  // 'seller'               => 448490530,  // opcional si coincide con el token
]);
```

**Detalle robusto de orden (**``** + fallback **``**)**: se añadió lógica para `title`, `doc`, `monto`, `medio_pago` y `dirección` con múltiples alternativas y uso de `/shipments/{id}` cuando falta dirección en la orden.

---

## 📦 CSV de salida (estructura)

```
ID Orden,Fecha,Nombre,DNI/CUIT,Monto,Productos,Dirección,Medio de pago
```

- **ID Orden**: numérico ML
- **Fecha**: `date_created`
- **Nombre**: `buyer.first_name + last_name`
- **DNI/CUIT**: `billing_info.doc_number` || `buyer.identification.number`
- **Monto**: `total_amount` || `paid_amount` || suma de pagos
- **Productos**: `item.title` + variaciones
- **Dirección**: `shipping.receiver_address.address_line` || `/shipments`
- **Medio de pago**: del primer pago (`payment_type`/`payment_type_id`)

---

## 🚀 Próximos pasos (opcionales)

- **Persistir** también en una tabla temporal (`ordenes_ml_tmp`) para auditoría y conciliación.
- **Parámetro** `--estado=paid|delivered|...` para flexibilizar filtros.
- **Validaciones** extra (CF sin doc) y banderas para reintentos.
- **Exportar a XLSX** directamente desde el comando.

---

## 🧩 Servicio **ArcaFacturar** — integración para el botón “Facturar”

**Objetivo:** emitir A/B/C contra AFIP (vía ARCA/WSFE) y reflejar resultado en `mlibre_orders` y `arca_facturar_logs`.

### Configuración requerida

- `.env` / `config/arca.php`: `ARCA_ENV`, `ARCA_CUIT`, `ARCA_CERT`, `ARCA_KEY`, `ARCA_KEY_PASS` y endpoints WSAA/WSFE.
- **Certificados** separados para homo/prod dentro de `storage/arca/` con permisos seguros.

### Flujo al presionar **Facturar**

1. El controlador arma el **payload** (cliente, items, totales, metadatos ML) y llama al **servicio** `arca.facturar` → método `emitir(payload)`.
2. El servicio resuelve **WSAA** (TA vigente), consulta **último número** y ejecuta **WSFE.FECAESolicitar** (A/B/C según doc/tipo receptor y PV).
3. Registra un **log** en `arca_facturar_logs` (status, http, request/response, error si aplica).
4. Si **OK** (resultado “A”):
   - marca `invoiced = true`, `arca_status = 'success'`, completa `invoice_type`, `pos_number`, `invoice_number`, `invoice_date`, `cae`, `cae_due_date`, `arca_invoice_id` y guarda el `arca_payload`.
5. Si **falla**: `arca_status = 'error'` y se guarda el mensaje en `arca_error` (quedando disponible para reintentar).

### Reglas mínimas sugeridas

- **Tipo de comprobante**: si `buyer_doc_type = CUIT` válido ⇒ **A**; si no ⇒ **B** (ajustable según condición IVA receptor).
- **IVA**: si no viene en cada ítem, calcular usando alícuota estándar (p.ej. 21%).
- **PV**: usar un punto de venta habilitado WS y tipo de comprobante correcto.

### Estados de facturación

- `pending` → importada, sin intentar.
- `queued`/`processing` → en cola o en curso.
- `success` → CAE asignado.
- `error` → fallo (visible en UI y en logs).

---

## 🔎 Importación — criterios clave

- **Consulta a **``: `seller = MLIBRE_USER_ID`, `order.status = paid`, rango `date_created`, `sort = date_desc`, paginado 50/offset.
- **Idempotencia**: upsert por `(seller_id, order_id)` en cabecera, reemplazo de hijos; no se pisa `invoiced` ni datos de CAE.
- **Dirección**: si falta en orden, `GET /shipments/{id}`.

---

## 🖥️ UI Blade — resumen

> 🆕 Extras:
>
> - **ID de orden** enlazado al detalle de ML (env `ML_ORDER_URL`).
> - Sub‑badge **Nota ML** con fecha/hora y preview del texto posteado.
> - Panel **Ver log** expandible incorpora snapshot de sincronización ML (HTTP/status).

- Tabla con: Orden, Fecha, Cliente, Doc, Monto, **Envío**, **Facturación** y **Acciones**.
- **Botón Facturar** (fila) habilitado solo si `status = paid` e `invoiced = false`.
- **Facturar seleccionadas**: envía lote, crea logs por cada una y actualiza estados.

---

## ▶️ Comandos útiles

- **Importar este año (hasta hoy):**
  ```bash
  php artisan mlibre:importar-ordenes 2025-01-01 $(date +%F) --estado=paid
  ```
- **Año calendario completo:**
  ```bash
  php artisan mlibre:importar-ordenes 2025-01-01 2025-12-31 --estado=paid
  ```

---

## 🏁 Conclusión

El **Importador de Órdenes Pagas** quedó **operativo y validado** con **persistencia en DB** y **panel de facturación**. Integrado a **ArcaFacturar**, el botón **Facturar** y el **lote** registran CAE y logs, permitiendo control y reintentos de manera simple y auditable.

---

## 🆕 Subobjetivo: Señalar la facturación en Mercado Libre (paso a paso)

### 1) Feature flags (.env)

```
# sincronización hacia ML tras facturar
ML_SYNC_NOTES=true          # A) crear nota interna con datos de la factura
ML_UPLOAD_INVOICE=false     # B1) subir PDF como factura (si tu cuenta/site lo permite)
ML_POSTSALE_MESSAGE=false   # B2) enviar PDF por mensajería post-venta (fallback)
```

### 2) Enganche en el controlador (tras `resp['ok']`)

En `Mlibre/OrdersController@facturarSeleccionados`, apenas confirmás CAE:

```php
$this->sincronizarEnML($o, $pdfPath ?? null); // $pdfPath si guardás el PDF localmente
```

### 3) Helpers mínimos

> Agregá el `use` del Http facade y del servicio de token en el controlador.

```php
private function sincronizarEnML($order, ?string $pdfPath = null): void
{
    $token = app(MlibreTokenService::class)->getValidAccessToken($order->seller_id);

    // A) Nota interna con los datos fiscales (si está habilitado)
    if (env('ML_SYNC_NOTES', true)) {
        $nota = $this->buildFacturaNote($order);
        Http::withToken($token)
            ->post("https://api.mercadolibre.com/orders/{$order->order_id}/notes", ['note' => $nota]);
    }

    // B) Compartir PDF con el comprador (opcional)
    if ($pdfPath && file_exists($pdfPath)) {
        if (env('ML_UPLOAD_INVOICE', false)) {
            $this->mlibreUploadInvoice($order->order_id, $pdfPath, $token);
        } elseif (env('ML_POSTSALE_MESSAGE', false)) {
            $this->mlibreEnviarMensajeConAdjunto($order->order_id, $pdfPath, $token);
        }
    }
}

private function buildFacturaNote($o): string
{
    $num = str_pad((string)($o->invoice_number ?? ''), 8, '0', STR_PAD_LEFT);
    return "Factura {$o->invoice_type} {$o->pos_number}-{$num} | CAE {$o->cae} (vto {$o->cae_due_date})";
}

// Stubs a completar según disponibilidad del site/permiso
private function mlibreUploadInvoice($orderId, string $pdfPath, string $token): void { /* TODO */ }
private function mlibreEnviarMensajeConAdjunto($orderId, string $pdfPath, string $token): void { /* TODO */ }
```

### 4) Logging/observabilidad

- Además del log de ARCA, podés registrar un log auxiliar de **nota subida** y/o **PDF compartido** con `status=success|error` y `http_code`.
- Si fallan estas llamadas, **no** hacer rollback de la factura: solo guardar el error y permitir reintento.

### 5) Pruebas rápidas

1. Facturá una orden de prueba desde `/mlibre/orders`.
2. Verificá en la orden de ML que aparezca la **nota** con CAE.
3. Si activaste `ML_UPLOAD_INVOICE` o `ML_POSTSALE_MESSAGE`, validá que el **PDF** esté disponible para el comprador.

---

## 🔍 Verificación en **ARCA → Mis Comprobantes** (día corriente)

**Observación:** la UI de *Mis Comprobantes* suele listar con **delay** (muchas veces desde el **día anterior hacia atrás**). El **CAE** es válido al instante, pero puede que **no** lo veas en la UI de ARCA el mismo día.

### ¿Cómo validar hoy mismo?

1. **Confirmación WSFE** por `tipo/pv/nro` (AFIP):

   - El controlador incluye `confirmarCaeEnAfip()` en modo **tolerante**. Si tu servicio implementa `feCompConsultar`, la confirmación será real.
   - Para forzar validación estricta, activar en `.env`:

   ```env
   ARCA_STRICT_CONFIRM=true
   ```

   - Si falta el método o el servicio no está disponible y `ARCA_STRICT_CONFIRM=false`, se salta la confirmación (no warning).

2. **Parser confiable de salida ARCA**: guardamos **PV** y **Nro** desde la línea `Emitiendo tipo=.., pto=.., nro=..` y además **CAE** y **Vto**, lo que permite una consulta exacta a AFIP cuando esté disponible.

### Reconciliación nocturna (sugerida)

Programar un job que **reconfirme** los CAE emitidos el día anterior y actualice estados (`success` ↔ `warning` si AFIP no valida):

```cron
# 01:00 todos los días — reconfirma el día anterior
0 1 * * * php /var/www/koi2/artisan mlibre:reconfirmar-cae --desde=$(date -d 'yesterday' +\%F) --hasta=$(date -d 'yesterday' +\%F)
```

> El comando `mlibre:reconfirmar-cae` es **opcional** (a implementar): recorre `mlibre_orders` en rango, usa `confirmarCaeEnAfip()` y ajusta `arca_status` + log.

### Checklist de control

- **Ambiente** y **CUIT** impresos en el stdout del comando (`Ambiente: produccion | CUIT: …`).
- **PV/Nro** persistidos en `pos_number` / `invoice_number`.
- **CAE/Vto** persistidos; Vto normalizado a `YYYY-MM-DD`.
- **Nota ML** creada (HTTP 201) y guardada (`ml_note_id`, fecha y texto).
- Si hoy no aparece en *Mis Comprobantes*, validar mañana o usar confirmación WSFE.

