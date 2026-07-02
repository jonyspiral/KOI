# Avance Lote C - PDF administracion - 2026-05-19

## Alcance de este sublote

Se normalizo el bloque `administracion/*/getPdf.php` como continuacion directa de los sublotes comercial y produccion de `Lote C`.

## Endpoints incluidos

Se relevaron y normalizaron 44 endpoints `getPdf.php` bajo `content/administracion/`, incluyendo estos grupos:

- `proveedores/*`
- `cajas/*`
- `reportes_gerenciales/*`
- `rrhh/*`
- `cobranzas/*`
- `finanzas/reportes/*`
- `tesoreria/*`
- `contabilidad/*`

## Cambio aplicado

Los 44 endpoints fueron normalizados con el mismo criterio ya usado en cliente, comercial y produccion:

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
- PDF administracion: `PREPARADO PARA SMOKE TEST`
- PDF sistema: `PENDIENTE`
- formularios de negocio (`Formulario*.php`): `PENDIENTE`

## Riesgo abierto

El principal riesgo ya no es de infraestructura PHP, sino de parametros y datasets reales por modulo:

- algunos reportes pueden requerir fechas, ids o filtros no triviales
- persiste riesgo de mensajes legacy con encoding defectuoso
- el criterio de aprobacion sigue siendo PDF valido o error de negocio controlado, no fatal PHP

## Smoke test recomendado

Validar primero un subconjunto representativo de administracion:

1. `administracion/proveedores/facturacion/getPdf.php`
2. `administracion/cajas/movimientos_caja/getPdf.php`
3. `administracion/tesoreria/egresos/orden_de_pago/getPdf.php`
4. `administracion/contabilidad/libro_diario/getPdf.php`

## Siguiente paso

Si los smoke tests de produccion y administracion no muestran regresiones de infraestructura, continuar con:

1. `content/sistema/*/getPdf.php`
2. formularios de negocio basados en `Html2Pdf`
