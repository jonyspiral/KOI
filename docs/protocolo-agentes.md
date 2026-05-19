# Protocolo de agentes - KOI1 Encinitas

## Objetivo
Mantener contexto técnico mínimo y decisiones operativas consistentes al trabajar sobre KOI1 legacy en entornos distintos:

- `C:\dev\encinitas_prod_real_20260515`: copia local limpia de producción KOI1 real / fuente primaria de análisis
- `X:\xampp\htdocs\encinitas`: producción KOI1 real, solo para refrescar la copia local cuando se decida explícitamente
- `encinitas`: adaptación Docker PHP 5.6 + MySQL 8

## Reglas
1. Antes de cambiar código, identificar explícitamente el entorno objetivo.
2. No asumir que un fix de MSSQL sirve en MySQL.
3. No asumir que `origin/main`, `fonts/evy`, `C:\dev\encinitas_prod_truth` o un backup intermedio representan la fuente real.
4. Para entender flujo legacy, comparar primero contra `C:\dev\encinitas_prod_real_20260515`.
5. Cuando un cambio toca acceso a datos, documentar:
   - tablas/vistas/SP afectados
   - si usa `EXEC`, `CALL`, query directa o shim
   - si el cambio aplica a ambos entornos o solo a uno
6. Si se toca un módulo legacy, actualizar `docs/INDEX.md` y agregar o ajustar una nota puntual en `docs/`.
7. Si el cambio fue validado solo localmente, dejarlo explícito.

## Checklist de sesión
1. Confirmar repo/entorno objetivo.
2. Revisar `.antigravity-context.md`.
3. Revisar `docs/INDEX.md`.
4. Ejecutar solo validaciones acordes al entorno.
5. Actualizar documentación al cerrar la sesión.

## Convenciones de documentación
- Documentar fechas absolutas (`2026-05-12`) en vez de "hoy" o "ayer".
- Separar claramente:
  - problema
  - causa raíz
  - fix aplicado
  - validación realizada
  - riesgos pendientes
