# Manifiesto de Procedencia por Tabla (Spiral -> Encinitas Test)

- Fecha auditoria: 2026-07-01 13:06:17
- Fuente triggers: `spiral_triggers_inventario.CSV`, `spiral_triggers_encinitas.CSV`
- Cruce de inventario productivo: `docs/migracion/produccion_operativa_spiral.csv`
- Objetos productivos unicos relevados: 55
- Tablas productivas auditadas: 37

## Clasificacion de procedencia
- CONFIRMADA_POR_TRIGGER_SPIRAL: 1
- POSIBLE_POR_TRIGGER: 1
- PRODUCTIVA_OBLIGATORIA_POR_INVENTARIO: 35

## Estado general de validacion
- BLOCKER: 24
- ERROR: 3
- OK: 10

## Hallazgos criticos
- Tablas faltantes en `encinitas_test` (2):
  - explosion_lote_temp [PRODUCTIVA_OBLIGATORIA_POR_INVENTARIO]
  - movimientos_almacen_mp [PRODUCTIVA_OBLIGATORIA_POR_INVENTARIO]
- Diferencias de PK detectadas (1):
  - Tareas_detalle: PK_DIFERENTE (spiral=nro_orden_fabricacion,nro_tarea,cod_seccion; target=id)

## Regla aplicada para no asumir cobertura por trigger
- Si una tabla productiva no tiene trigger sync explicito, se clasifica `PRODUCTIVA_OBLIGATORIA_POR_INVENTARIO`.
- Si existe solo en target sin evidencia de sync desde Spiral, se clasifica `LOCAL_SIN_EVIDENCIA_DE_SYNC`.
- Las validaciones de tipo permiten `VIEW` en target como alias compatible por nombre logico.

## Artefactos
- Detalle completo CSV: `docs/migracion/manifiesto_procedencia_tablas_spiral.csv`
