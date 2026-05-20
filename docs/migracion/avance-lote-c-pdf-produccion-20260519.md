# Avance Lote C - PDF produccion - 2026-05-19

## Alcance de este sublote

Se normalizo el bloque `produccion/*/getPdf.php` como continuacion directa del sublote comercial de `Lote C`.

## Endpoints incluidos

- `content/produccion/compras/ordenes_compra/pendiente/getPdf.php`
- `content/produccion/gestion_produccion/confirmacion/getPdf.php`
- `content/produccion/producto/reportes/costos_articulos/getPdf.php`
- `content/produccion/reportes/programacion_empaque/getPdf.php`
- `content/produccion/stock/movimientos/getPdf.php`
- `content/produccion/stock/stock_a_fecha/getPdf.php`
- `content/produccion/stock_mp/movimientos/getPdf.php`
- `content/produccion/stock_mp/stock_a_fecha/getPdf.php`

## Cambio aplicado

Los 8 endpoints fueron normalizados con el mismo criterio ya usado en cliente y comercial:

- `ob_start()` al inicio
- limpieza de salida residual con `ob_clean()` despues del bootstrap
- `shutdown_function` para fatales reales en JSON
- chequeo explicito de permiso y usuario logueado
- eliminacion del patron legacy `<?php require_once(...); if (...) { ?>`
- escritura en UTF-8 sin BOM

No se modifico la logica de cada reporte ni sus parametros; solo el bootstrap y el manejo defensivo de salida/errores.

## Estado

Subestado actual de `Lote C`:

- PDF comercial: `VALIDADO`
- PDF produccion: `PREPARADO PARA SMOKE TEST`
- PDF administracion: `PENDIENTE`
- formularios de negocio (`Formulario*.php`): `PENDIENTE`

## Smoke test recomendado

Orden minimo de validacion en Ubuntu/contenedor:

1. `produccion/stock/stock_a_fecha/getPdf.php`
2. `produccion/stock/movimientos/getPdf.php`
3. `produccion/stock_mp/stock_a_fecha/getPdf.php`
4. `produccion/producto/reportes/costos_articulos/getPdf.php`

Criterio de aprobacion:

- respuesta PDF valida (`file /tmp/*.pdf` => `PDF document`), o
- error de negocio controlado, sin fatal PHP ni salida basura previa

## Siguiente paso

Si el smoke test de produccion no muestra regresiones de infraestructura, continuar con:

1. `administracion/*/getPdf.php`
2. formularios de negocio basados en `Html2Pdf`