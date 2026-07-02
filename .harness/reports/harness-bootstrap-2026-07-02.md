# Harness bootstrap report - 2026-07-02

## Diagnostico inicial
- Ruta objetivo validada: `C:\dev\koi`.
- Repo detectado: `jonyspiral/KOI`.
- Rama inicial detectada: `main`.
- Arquitectura detectada: KOI legacy PHP custom (no Laravel).
- `AGENTS.md` no existe en KOI.

## Cambios heredados detectados antes del bootstrap
Se validaron y conservaron exactamente estos cuatro cambios preexistentes:
- `.antigravity-context.md`
- `docs/INDEX.md`
- `docs/protocolo-agentes.md`
- `docs/infraestructura/koi-legacy-topologia.md`

## Estructura Harness creada
```text
.harness/
  project.yml
  rules.md
  context.md
  current-task.md
  decisions.md
  open-issues.md
  checks/
    verify-odbc-preflight.ps1
  docs/
  prompts/
    .gitkeep
  reports/
    .gitkeep
    harness-bootstrap-2026-07-02.md
```

## Datos ODBC incorporados
Se incorporaron en `.harness/project.yml` los perfiles sin passwords:
- `KOI1_MYSQL_PROD_RW` (host `192.168.2.210`, puerto `3306`, db `koi1_prod`, user `koi_odbc`, charset `utf8mb4`, SSL solicitado `Preferred`).
- `KOI1_MYSQL_STAGE_RW` (host `192.168.2.210`, puerto `3306`, db `koi1_stage`, user `koi_odbc`, charset `utf8mb4`, SSL solicitado `Preferred`, uso baseline/rollback).

## Riesgos abiertos
- MySQL expuesto en `0.0.0.0:3306`.
- UFW inactivo.
- Solo confirmado `koi_odbc@192.168.2.44`.
- No asumir grants para otras PCs sin validacion.
- `SSL Mode=Preferred` no prueba TLS efectivo.
- No versionar passwords en DSN.
- `koi1_stage` no es entorno diario.
- No realizar cambios de grants/firewall/MySQL/DSN durante bootstrap.

## Checks seguros creados
- `.harness/checks/verify-odbc-preflight.ps1`
  - Usa `Test-NetConnection` (host/port parametrizables).
  - Detecta drivers ODBC MySQL x86/x64 por lectura de registro.
  - Recomienda administrador ODBC segun arquitectura de Access (entrada o auto-deteccion).
  - No crea DSN, no pide passwords, no abre sesion autenticada, no modifica registro.

## Checks no ejecutados
- No se ejecuto `verify-odbc-preflight.ps1` en este bootstrap para mantenerlo documental/estructural.
- No se ejecutaron comandos sobre servidores, Docker, Nginx, MySQL o SQL Server.

## Confirmaciones de seguridad operativa
- No hubo cambios en servidor ni infraestructura.
- No hubo cambios en DB ni secretos.
- No hubo deploy.
- No hubo `git add`.
- No hubo commit.
- No hubo push.
- No hubo merge/rebase/reset/clean.

## Evidencia final requerida
La evidencia final del estado queda en la salida de:
- `git status --short --untracked-files=all`
- `git diff --stat`
- `git diff --cached --stat`
