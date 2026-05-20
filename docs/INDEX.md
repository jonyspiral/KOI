# ÃƒÂndice de documentaciÃƒÂ³n - KOI1 Encinitas

## OperaciÃƒÂ³n y contexto
- `../.antigravity-context.md` - snapshot operativo del repo y del entorno Docker
- `protocolo-agentes.md` - reglas de trabajo y documentaciÃƒÂ³n para sesiones tÃƒÂ©cnicas

## MigraciÃƒÂ³n MySQL
- `migracion/mysql-case-sensitive-aliases.md` - notas de compatibilidad MySQL, collation y aliases case-sensitive
- `migracion/metodo-recuperacion-contexto-20260515.md` - linea de recuperacion, commits de control y metodo de trabajo
- `migracion/estado-recuperacion-render-20260515.md` - estado del hito donde vuelven a renderizar login, catalogo cliente y BO
- `migracion/plan-trabajo-migracion-20260519.md` - retorno al esquema formal de trabajo por fases y lotes de migracion
- `migracion/cierre-lote-a-normalizacion-20260519.md` - cierre operativo del baseline tecnico y salida a Lote B
- `migracion/avance-lote-b-favoritos-20260519.md` - cierre funcional de favoritos batch y hallazgo del mutex Linux
- `migracion/avance-lote-b-pedidos-20260519.md` - cierre funcional de pedidos cliente, PDF en contenedor y hardening final del Lote B
- `migracion/avance-lote-c-pdf-comercial-20260519.md` - cierre funcional del sublote PDF comercial
- `migracion/avance-lote-c-pdf-produccion-20260519.md` - normalizacion del sublote PDF de produccion y smoke test recomendado
- `migracion/avance-lote-c-pdf-administracion-20260519.md` - normalizacion del sublote PDF de administracion y smoke test recomendado
- `migracion/avance-lote-c-pdf-sistema-formularios-20260519.md` - normalizacion del ultimo endpoint PDF de sistema y fix puntual en formularios
- `migracion/avance-lote-d-bo-interactivo-20260519.md` - seleccion del primer ABM y de la primera pantalla BO para el siguiente lote
- `migracion/smoke-test-pdf-produccion-20260519.md` - checklist minimo para el siguiente sublote PDF de produccion
- `migracion/smoke-test-lote-d-bo-20260519.md` - checklist del primer smoke test de BO interactivo

## Notas sugeridas a crear a medida que avance la migraciÃƒÂ³n
- `migracion/stored-procedures-mysql.md`
- `modulos/gestion-cobranza.md`
- `modulos/gestion-proveedores.md`
