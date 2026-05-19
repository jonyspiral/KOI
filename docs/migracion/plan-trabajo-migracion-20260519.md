# Plan de trabajo y migracion - 2026-05-19

## Objetivo
Volver del modo recuperacion al modo migracion, tomando como baseline el estado funcional recuperado en mayo de 2026: login, render cliente, catalogo con filtros y render de BO en KOI1 sobre PHP 5.6 + MySQL.

## Baseline funcional actual
Se considera baseline funcional el estado recuperado y validado despues del hito documentado en `estado-recuperacion-render-20260515.md`.

Este baseline no implica homogeneidad completa del sistema. Implica que ya existe una base hibrida funcional sobre la que conviene normalizar y migrar por lotes, no reabrir una migracion total desde cero.

## Criterio de clasificacion
Cada modulo o area debe quedar etiquetado como uno de estos estados:

- `MIGRADO`: ya corre en PHP 5.6/MySQL con comportamiento aceptable.
- `HIBRIDO`: corre parcialmente, pero depende de compatibilidad puntual, datos heredados o codigo legacy.
- `LEGACY`: sigue atado a SQL Server, `mssql_*`, sintaxis T-SQL o infraestructura vieja.
- `DESCONOCIDO`: todavia no fue relevado con evidencia suficiente.

## Fases de trabajo

### Fase 0. Estabilizacion del baseline
Objetivo: congelar el estado recuperado y evitar volver al modo reparacion reactiva.

Incluye:
- push/pull del commit de recuperacion
- smoke test cliente y BO
- confirmacion de entorno Docker/PHP 5.6/MySQL
- registro de riesgos abiertos sin mezclar limpieza todavia

Criterio de cierre:
- baseline subido a git
- baseline replicado en entorno local
- smoke test minimo aprobado

### Fase 1. Normalizacion
Objetivo: convertir parches de recuperacion en reglas tecnicas estables.

Incluye:
- normalizacion de aliases y nombres de campos sensibles a encoding (`ñ` vs ASCII)
- normalizacion de nombres case-sensitive en MySQL/Linux
- correccion de firmas incompatibles y warnings heredados
- consolidacion de `Config`, `pathBase`, `includes`, `findRealPath`
- limpieza controlada de ruido tecnico versionado si no afecta la operacion

Criterio de cierre:
- sin warnings conocidos en rutas criticas del cliente
- sin fallos por case sensitivity ya detectados
- documentacion de decisiones de compatibilidad consolidada

### Fase 2. Motor comun de compatibilidad MySQL
Objetivo: dejar una base repetible para modulos hibridos y legacy.

Incluye:
- `factory/Config.php`
- `factory/Datos.php`
- `factory/drivers/DbMysql.php`
- `clases/Base.php`
- `factory/Mapper.php`
- helpers o capa pragmatica para compatibilidad SQL

Criterio de cierre:
- consultas comunes resueltas sin depender de parches aislados por modulo
- infraestructura minima lista para migrar lotes adicionales

### Fase 3. Inventario por estado
Objetivo: dejar de pensar la app por carpetas y pasar a una matriz de migracion por estado real.

Incluye:
- relevamiento de modulos cliente
- relevamiento de BO critico
- identificacion de dependencias a SQL Server o T-SQL
- identificacion de stored procedures y puntos de alto riesgo

Criterio de cierre:
- matriz `MIGRADO/HIBRIDO/LEGACY/DESCONOCIDO` por modulo o area
- backlog priorizado por impacto

### Fase 4. Migracion por lotes funcionales
Objetivo: avanzar por flujo de negocio y no por refactor total.

Lotes sugeridos:
- cliente: catalogo, favoritos, pedidos, PDF, detalle de modelo
- BO: ABMs y pantallas criticas
- reportes y procesos batch
- legacy puro pendiente

Criterio de cierre:
- cada lote termina con smoke test, riesgos abiertos y nota de avance

## Orden recomendado inmediato

### Lote A. Normalizacion core
- consolidar fixes de recuperacion
- revisar aliases ASCII/UTF-8
- revisar helpers de ruta y carga comun
- aislar artefactos y backups fuera del flujo normal

### Lote B. Cliente
- favoritos
- pedido completo
- listado de pedidos
- PDF y detalle de modelo

### Lote C. BO
- validar render y navegacion
- validar al menos un ABM simple y una pantalla sensible a datos

### Lote D. Infraestructura heredada
- endurecer `Base`, `Mapper`, `Factory`, `Datos`
- identificar compatibilidad SQL reutilizable

## Definicion de terminado por lote
Cada lote debe cerrar con:
- estado funcional verificado
- archivos tocados
- riesgos abiertos
- smoke test ejecutado
- documento de avance en `docs/migracion/`

## Riesgos asumidos
- diferencias por collation, case sensitivity y encoding
- alias heredados con caracteres no ASCII
- dependencias parciales a SQL Server todavia no relevadas
- queries complejas o stored procedures que no se resuelven con traduccion mecanica

## Proxima sesion recomendada
La proxima sesion deberia enfocarse en `Fase 1 - Normalizacion`, comenzando por:
- limpieza de encoding y aliases sensibles
- consolidacion de helpers de carga y compatibilidad ya tocados
- smoke test funcional de cliente y BO sobre baseline estable
