CREATE VIEW dbo.[tareas_sin empaque_v]
AS
SELECT     TOP 100 PERCENT *
FROM         (SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, 
                               