CREATE VIEW dbo.asignados_por_tarea
AS
SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan AS Lote, dbo.Tareas_cabecera.nro_orden_fabricacion, dbo.Tareas_cabecera.nro_tarea, 
                      dbo.Orden_fabricacion.cod_articulo, dbo.Orden_fab              ORDER BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea) a ON 
                      dbo.Tareas_cabecera.nro_orden_fabricacion = a.nro_orden_fabricacion AND dbo.Tareas_cabecer