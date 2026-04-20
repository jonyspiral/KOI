CREATE VIEW [dbo].[mp_consumos_vw] AS
CREATE VIEW dbo.mp_consumos_vw
AS
SELECT     dbo.Tareas_detalle.fecha_salida_real AS fecha_movimiento, dbo.Consumos_tarea.cod_material, dbo.Consumos_tarea.cod_color, 
                      - (1 * ISNULL(dbo.Consumos_tarea.cant_consumo, 0)) AS Cant, - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_1, 0) END)) AS c1, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_2, 0) END)) AS c2, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_3, 0) END)) AS c3, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_4, 0) END)) AS c4, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_5, 0) END)) AS c5, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_6, 0) END)) AS c6, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_7, 0) END)) AS c7, 
                      - (1 * (CASE m.usa_rango WHEN 'N' THEN 0 ELSE ISNULL(dbo.Consumos_tarea.cant_8, 0) END)) AS c8, '01' AS cod_almacen, 
                      'consumo tarea: ' + CAST(dbo.Consumos_tarea.nro_orden_fabricacion AS VARCHAR) + '-' + CAST(dbo.Consumos_tarea.nro_tarea AS VARCHAR) AS Motivo
FROM         dbo.Consumos_tarea INNER JOIN
                      dbo.Tareas_detalle ON dbo.Consumos_tarea.nro_orden_fabricacion = dbo.Tareas_detalle.nro_orden_fabricacion AND 
                      dbo.Consumos_tarea.nro_tarea = dbo.Tareas_detalle.nro_tarea AND dbo.Consumos_tarea.cod_seccion = dbo.Tareas_detalle.cod_seccion INNER JOIN
                      dbo.Tareas_cabecera ON dbo.Tareas_detalle.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion AND 
                      dbo.Tareas_detalle.nro_tarea = dbo.Tareas_cabecera.nro_tarea INNER JOIN
                      dbo.materiales AS m ON dbo.Consumos_tarea.cod_material = m.cod_material

GO
