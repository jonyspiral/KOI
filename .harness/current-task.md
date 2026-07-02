# Current Task - KOI

## Tarea activa
Bootstrap Harness KOI legacy.

## Scope
- Crear estructura `.harness/` local en `C:\dev\koi`.
- Documentar contexto y reglas del harness local.
- Incorporar perfiles ODBC sin password y check preflight read-only.

## Fuera de scope
- Cambios en servidores, Docker, Nginx, MySQL, SQL Server o produccion.
- Deploys, migraciones, syncs, escrituras DB.
- Git add/commit/push/merge/rebase/reset/clean.

## Autorizaciones registradas
### 2026-07-02 - Autorizacion explicita de Vicente
- Scope: creacion del Harness local y documentacion ODBC.
- Entorno afectado: `C:\dev\koi` solamente.
- Acciones autorizadas:
  - validar estado git local
  - crear rama `chore/koi-harness-bootstrap`
  - crear y completar archivos bajo `.harness/`
  - actualizar solo documentos permitidos del repo
- Acciones todavia prohibidas:
  - tocar produccion, servidores, DB, secretos, deploy
  - ejecutar git add/commit/push/merge/rebase/reset/clean
- Evidencia esperada:
  - `git status --short --untracked-files=all`
  - `git diff --stat`
  - reporte final en `.harness/reports/`

## Estado de finalizacion
- [x] Validacion de worktree heredado
- [x] Creacion de rama de bootstrap
- [ ] Creacion completa de artefactos `.harness/`
- [ ] Emision de reporte final
- [ ] Entrega para revision humana
