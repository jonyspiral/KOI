/*TAREAS INCUMPLIAS

*/
CREATE VIEW dbo.tareas_incumplidas_empaque_v
AS
SELECT     *, nro_plan AS [plan], nro_orden_fabricacion AS op, nro_tarea AS tarea
FROM         (SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.