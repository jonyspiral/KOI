# Decisions - Harness local KOI

## 2026-07-02 - Adopcion de harness local en KOI
- Se adopta `.harness/` como capa de contexto operativo del proyecto KOI legacy.
- El harness pertenece al sistema KOI y no al modelo.

## 2026-07-02 - Relacion con Asteroides
- `C:\dev\asteroides-harness` se usa como referencia de filosofia y estructura general.
- Las plantillas globales vacias no se copian literalmente; se completa contenido especifico de KOI.

## 2026-07-02 - Relacion con KOI2
- `C:\dev\koi2\.harness` y `C:\dev\koi2\AGENTS.md` se usan solo como referencia estructural.
- No se importan rutas, contratos tecnicos, comandos operativos, bases de datos ni configuraciones de KOI2.

## 2026-07-02 - Relacion con AGENTS.md y docs existentes
- KOI no tiene `AGENTS.md`; en este bootstrap no se crea.
- El contexto operativo queda distribuido entre `.harness/` y documentacion existente en `docs/`.
- `docs/infraestructura/koi-legacy-topologia.md` pasa a referenciar `.harness/project.yml` como fuente estructurada principal.
