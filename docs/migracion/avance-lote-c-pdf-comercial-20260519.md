# Avance Lote C - PDF comercial - 2026-05-19

## Alcance de este sublote

Se inicio la continuacion post `Lote B - Cliente` enfocando el siguiente frente reusable: endpoints `getPdf.php` del area comercial que consumen `Html2Pdf`.

Motivo de prioridad:

- ya existe base tecnica corregida en `Html2Pdf.php`, `KoiServices.php` y runtime Docker
- el area comercial tenia un bloque acotado de 9 endpoints PDF
- era el siguiente tramo con mejor relacion entre cobertura y riesgo

## Inventario del sublote comercial

Endpoints relevados y normalizados en este avance:

- `content/comercial/cuenta_corriente/getPdf.php`
- `content/comercial/ecommerce/panel_de_control/getPdf.php`
- `content/comercial/ecommerce/reporte_ventas/getPdf.php`
- `content/comercial/pedidos/estadisticas/getPdf.php`
- `content/comercial/pedidos/historico/getPdf.php`
- `content/comercial/pedidos/pendientes/getPdf.php`
- `content/comercial/reportes/listado_clientes/getPdf.php`
- `content/comercial/reportes/predespachos/getPdf.php`
- `content/comercial/stock/getPdf.php`

## Cambio aplicado

Los 9 endpoints fueron normalizados con el mismo criterio usado en cliente:

- `ob_start()` al inicio
- limpieza de salida residual con `ob_clean()` luego del bootstrap
- `shutdown_function` para exponer fatales reales en JSON
- chequeo explicito de permiso y usuario logueado
- eliminacion del patron legacy `<?php require_once(...); if (...) { ?>`
- escritura en UTF-8 sin BOM

No se cambio la logica de negocio de cada reporte; solo se endurecio el bootstrap y el manejo de errores.

## Estado del frente PDF

Inventario actual de consumers activos de `Html2Pdf` al abrir `Lote C`:

- `administracion`: 44 endpoints `getPdf.php`
- `comercial`: 9 endpoints `getPdf.php`
- `produccion`: 8 endpoints `getPdf.php`
- `sistema`: 1 endpoint `getPdf.php`
- formularios de negocio (`clases/Formulario*.php`): 18 clases consumidoras

## Evidencia minima de validacion

- `comercial/stock/getPdf.php`: respuesta validada como `PDF document`
- `comercial/reportes/listado_clientes/getPdf.php`: respuesta PDF sin error JSON visible
- el sublote comercial queda funcionalmente validado sobre runtime Ubuntu/contenedor

## Riesgo y criterio de continuidad

Riesgos abiertos:

- algunos reportes conservan textos legacy con mojibake en titulos o mensajes, aunque ya no bloquean el flujo
- varios reportes dependen de `buscar.php` y de parametros GET que deben probarse con datos reales
- el runtime sigue dependiendo de `wkhtmltopdf` + `xvfb` dentro del contenedor `koi1-php56`

## Siguiente paso recomendado

Continuar con:

1. `produccion/*/getPdf.php`
2. `administracion/*/getPdf.php`
3. formularios de negocio (`Formulario*.php`)
