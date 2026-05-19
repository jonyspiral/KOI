# Cierre de Lote A - Normalizacion Core - 2026-05-19

## Objetivo del lote
Cerrar la salida del modo recuperacion y dejar un baseline tecnico estable para retomar la migracion por lotes.

## Estado alcanzado
- La raiz `/` responde y login cliente renderiza.
- El catalogo cliente vuelve a renderizar con `CatalogoCtrl`, `#catalogo` e items visibles.
- El menu lateral vuelve a filtrar por familias y por tipo de producto stock.
- El BO vuelve a renderizar.
- Se eliminaron warnings bloqueantes observados durante la recuperacion:
  - `Strict Standards` por firmas incompatibles
  - `Undefined index` en aliases sensibles de catalogo
- Se normalizaron los filtros default `01/02/03/04`.
- Se corrigio `findRealPath()` para resolver rutas relativas reales antes de usar `include_path`.
- Se normalizo el ruido de baseline:
  - `.gitignore` minimo
  - limpieza de `Thumbs.db` trackeados
  - aislamiento de backups y artefactos locales

## Excepcion abierta pero aislada
- El caso `clases/Html.php` / `clases/HTML.php` sigue condicionado por el mount RaiDrive/SFTP.
- El baseline funcional actual mantiene la carga explicita de `clases/Html.php.bak` desde `includes.php`.
- Esta excepcion se considera de entorno, no un bloqueo de codigo para avanzar al siguiente lote.

## Checklist de cierre

### Baseline tecnico
- `Config.php` alineado a runtime Linux + MySQL de KOI1 stage.
- `includes.php` estable para autoload y carga comun.
- `master.php` estable para resolucion de vistas.
- `Mapper.php` estable para catalogo y aliases sensibles de lectura.

### Verificacion minima recomendada
- Login cliente.
- `/catalogo/?c=1&f=all`.
- Cambio entre familias del lateral.
- Render BO basico.

### Riesgos que pasan al siguiente lote
- Excepcion de filesystem case-sensitive bajo RaiDrive/SFTP.
- Posibles aliases heredados con `ñ` fuera de los flujos ya ejercitados.
- Validacion funcional profunda pendiente en favoritos y pedidos.

## Salida del lote
Con este lote cerrado, la siguiente sesion debe arrancar en:

- `Fase 1 / Lote B - Cliente`

Prioridades:
- favoritos
- pedido completo
- listado de pedidos
- PDF
- detalle de modelo
