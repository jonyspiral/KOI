# Avance Lote B - Favoritos batch - 2026-05-19

## Resultado

Se cerró la recuperacion funcional del flujo batch de favoritos en cliente sobre KOI1 PHP 5.6 + MySQL.

Validacion manual reportada en runtime Ubuntu:

- `content/cliente/favoritos/agregarVarios.php`
- `content/cliente/favoritos/borrarVarios.php`

Con payload real:

- `idArticulo = 3202`
- `idColorPorArticulo = RSN`

Respuestas observadas:

- `agregarVarios.php` responde `status=200`
- `borrarVarios.php` responde `status=200`

## Problema real encontrado

El fallo no estaba en la logica de favoritos sino en la infraestructura de locking legacy.

`factory/Mutex.php` estaba implementado de forma inconsistente para Linux:

- en `initializeMutex()` lanzaba excepcion fuera de Windows
- en `lock()` lanzaba excepcion fuera de Windows
- en `unlock()` lanzaba excepcion fuera de Windows

Eso terminaba rompiendo persistencia y borrado con el fatal:

- `Call to a member function unlock() on null`
- archivo: `clases/Base.php`

## Correccion aplicada

Se reemplazo `factory/Mutex.php` por una implementacion basada en archivo + `flock()` compatible con:

- Windows
- Linux

Ademas, los endpoints batch de favoritos quedaron endurecidos para:

- limpiar salida espuria antes del JSON
- exponer fatales como JSON util durante la fase de recuperacion

## Estado del lote

Subestado actualizado dentro de `Lote B - Cliente`:

- favoritos batch: `MIGRADO`
- favoritos single/edit/reporte: `HIBRIDO`
- pedidos: `PENDIENTE DE VALIDACION PROFUNDA`

## Riesgos abiertos

- `factory/Mutex.php` llego a verse con BOM en runtime despues de la reescritura; conviene verificar encoding final al consolidar el lote
- `content/cliente/pedidos/*` sigue con mezcla de tags PHP legacy, mensajes con mojibake y manejo de errores heterogeneo
- `clases/Base.php` conserva un patron de `unlock()` poco defensivo aunque dejo de ser bloqueante al corregir `Mutex.php`

## Siguiente frente recomendado

Continuar con `pedidos` en este orden:

- `content/cliente/pedidos/agregar.php`
- `content/cliente/pedidos/borrar.php`
- `content/cliente/pedidos/index.php`
- `content/cliente/pedidos/getPdf.php`

Objetivo inmediato:

- validar alta de pedido desde favoritos reales
- validar cancelacion
- validar listado y PDF
