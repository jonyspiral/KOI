# Rules - Harness local KOI legacy

## Orden obligatorio de lectura
1. `.harness/project.yml`
2. `.harness/rules.md`
3. `.harness/context.md`
4. `.harness/current-task.md`
5. `.harness/decisions.md`
6. `.harness/open-issues.md`
7. `docs/infraestructura/koi-legacy-topologia.md`
8. `docs/protocolo-agentes.md`
9. `docs/INDEX.md`

## Acciones prohibidas por defecto
- No ejecutar git add, commit, push, merge, rebase, reset --hard ni clean -fd.
- No tocar Ubuntu, Docker, Nginx, MySQL, SQL Server, MikroTik, DNS ni produccion.
- No modificar `.env`, `factory/Config.php`, certificados, claves, tokens o secretos.
- No ejecutar deploy, migraciones, syncs ni escrituras de base de datos.

## Politica de autorizaciones
- Toda autorizacion explicita de Vicente se registra en `.harness/current-task.md`.
- La autorizacion registrada debe incluir: fecha, scope, entorno afectado, autorizado, prohibido y evidencia esperada.
- Sin registro explicito, se mantiene modo solo lectura.

## Criterio para cambios estructurales
- Preferir actualizar archivos existentes antes de crear duplicados.
- No crear documentacion vacia por simetria con otros repos.
- Toda estructura nueva debe estar justificada por scope vigente.

## Regla de contratos tecnicos
- No inventar contratos, endpoints, dependencias, rutas, bases ni comandos.
- Verificar contra archivos reales del repo KOI y contra autorizaciones vigentes.
- KOI2 es solo referencia de estructura de harness, no de configuracion tecnica operativa.

## Proteccion de produccion, DB y secretos
- Produccion KOI legacy y rollback son entornos de referencia operativa, no de modificacion en bootstrap.
- No guardar passwords ni secretos en el repo.
- Stage (`koi1_stage`) es baseline/rollback; no usar como entorno diario sin autorizacion.
