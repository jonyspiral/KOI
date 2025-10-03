CREATE VIEW dbo.consumos_comprometidos_v
AS
SELECT     dbo.tareas_incumplidas_v.nro_plan, dbo.tareas_incumplidas_v.nro_orden_fabricacion, dbo.tareas_incumplidas_v.nro_tarea, 
                      dbo.tareas_incumplidas_v.cantidad, dbo.patrones_v.cod_a