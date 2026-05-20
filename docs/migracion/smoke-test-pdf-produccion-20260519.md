# Smoke Test PDF Produccion - 2026-05-19

Objetivo: validar el siguiente sublote de `Lote C` sobre runtime Ubuntu/contenedor `koi1-php56`.

## Endpoints objetivo

- `produccion/compras/ordenes_compra/pendiente/getPdf.php`
- `produccion/gestion_produccion/confirmacion/getPdf.php`
- `produccion/producto/reportes/costos_articulos/getPdf.php`
- `produccion/reportes/programacion_empaque/getPdf.php`
- `produccion/stock/movimientos/getPdf.php`
- `produccion/stock/stock_a_fecha/getPdf.php`
- `produccion/stock_mp/movimientos/getPdf.php`
- `produccion/stock_mp/stock_a_fecha/getPdf.php`

## Criterio minimo de aprobacion

Cada endpoint debe cumplir al menos una de estas condiciones:

- responder binario PDF valido (`file /tmp/*.pdf` => `PDF document`)
- devolver error de negocio controlado, pero no fatal PHP ni salida basura previa

## Comando de validacion sugerido

```bash
curl -s -b /tmp/koi.cookie -c /tmp/koi.cookie -o /tmp/test.pdf "URL_DEL_ENDPOINT"
file /tmp/test.pdf
```

## Orden recomendado

1. `produccion/stock/stock_a_fecha/getPdf.php`
2. `produccion/stock/movimientos/getPdf.php`
3. `produccion/stock_mp/stock_a_fecha/getPdf.php`
4. `produccion/producto/reportes/costos_articulos/getPdf.php`

Los cuatro restantes pueden validarse despues si los primeros no muestran regresiones de infraestructura.