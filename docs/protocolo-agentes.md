# Protocolo de agentes - KOI legacy

## Objetivo
Mantener decisiones operativas consistentes para KOI legacy sin mezclarlo con KOI2.

## Reglas de trabajo
1. Antes de cualquier cambio, confirmar repo objetivo `C:\dev\koi`.
2. Usar `.harness/project.yml` como fuente estructurada principal del harness local.
3. Usar como fuente operativa canonica `docs/infraestructura/koi-legacy-topologia.md`.
4. No usar rutas legacy historicas del host (`/var/www/encinitas`, `/var/www/encinitas_test_runtime`) como destino operativo.
5. No ejecutar git pull/merge/reset/checkout en `/var/www/koi` sin autorizacion explicita.
6. No modificar Nginx publico, certificados, DNS o MikroTik sin autorizacion explicita.
7. No tocar `/var/www/koi_rollback_20260702_094857`.
8. No versionar `factory/Config.php` ni archivos `*.bak` productivos.
9. Si cambia topologia o limites operativos, actualizar `.harness/project.yml`, `docs/infraestructura/koi-legacy-topologia.md` y luego `docs/INDEX.md`.

## Checklist de sesion
1. Revisar `.antigravity-context.md`.
2. Revisar `.harness/project.yml`.
3. Revisar `.harness/rules.md` y `.harness/current-task.md`.
4. Revisar `docs/INDEX.md`.
5. Revisar `docs/infraestructura/koi-legacy-topologia.md`.
6. Ejecutar solo comandos de verificacion no destructivos salvo autorizacion explicita.
7. Documentar validacion realizada y fecha absoluta.

## Convenciones de documentacion
- Usar fechas absolutas (ejemplo `2026-07-02`).
- Separar problema, decision, validacion y riesgos.
- No incluir credenciales, secretos ni `Config.php` productivo.
