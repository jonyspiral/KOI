# Context - KOI legacy

## Arquitectura confirmada
- Aplicacion legacy PHP custom (sin estructura Laravel estandar).
- Estructura principal observada: `factory/`, `clases/`, `content/`, `includes/`, `js/`, `css/`, `docs/`, `scripts/`.
- El runtime legacy del contenedor conserva `/var/www/encinitas` por compatibilidad interna.

## Modulos reales detectados
- Nucleo de negocio y acceso a datos en `factory/` y `clases/`.
- Flujos funcionales legacy en `content/`.
- Scripts de verificacion/auditoria en `scripts/`:
  - `koi-parity-audit.php`
  - `koi-functional-parity.php`
  - `auditar-procedencia-tablas.ps1`

## Dependencias externas detectadas en repo
- PHPExcel
- PHPMailer
- AngularJS
- Bootstrap
- jQuery
- Highcharts

## Flujos importantes
- Operacion KOI legacy en contenedor `koi1-php56` (produccion via proxy nginx a `127.0.0.1:8196`).
- Rollback/baseline en `koi1-php56_test` (`8195`, solo LAN, sin exposicion publica intencional).

## Contratos confirmados
- Fuente estructurada principal del harness: `.harness/project.yml`.
- Orden de lectura y restricciones: `.harness/rules.md`.
- Registro de autorizaciones: `.harness/current-task.md`.

## Riesgos actuales
- Worktree contiene cambios heredados preexistentes del bootstrap previo.
- KOI no tiene `AGENTS.md`; el contexto operativo debe resolverse desde `.harness/` + `docs/`.
- No asumir equivalencia de arquitectura con KOI2.

## Hechos pendientes de validar
- Validacion por PC de los DSN ODBC documentados.
- Confirmacion de arquitectura Access por equipo cliente antes de elegir admin ODBC final.
- Confirmacion de negociacion TLS real cuando se use `SSL Mode=Preferred`.
