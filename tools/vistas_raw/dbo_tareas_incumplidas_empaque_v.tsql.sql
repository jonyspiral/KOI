CREATE VIEW [dbo].[tareas_incumplidas_empaque_v] AS
/*TAREAS INCUMPLIAS

*/
CREATE VIEW dbo.tareas_incumplidas_empaque_v
AS
SELECT     *, nro_plan AS [plan], nro_orden_fabricacion AS op, nro_tarea AS tarea
FROM         (SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, 
                                              dbo.Tareas_cabecera.cantidad, dbo.Tareas_detalle.cod_seccion, dbo.Orden_fabricacion.cod_articulo, dbo.articulos.denom_articulo, 
                                              dbo.Orden_fabricacion.cod_color_articulo, dbo.Orden_fabricacion.version, dbo.Tareas_cabecera.tipo_tarea, 
                                              dbo.Tareas_cabecera.pos_1_cant, dbo.Tareas_cabecera.pos_2_cant, dbo.Tareas_cabecera.pos_4_cant, dbo.Tareas_cabecera.pos_3_cant, 
                                              dbo.Tareas_cabecera.pos_5_cant, dbo.Tareas_cabecera.pos_6_cant, dbo.Tareas_cabecera.pos_7_cant, 
                                              dbo.Tareas_cabecera.pos_8_cant
                       FROM          dbo.Orden_fabricacion INNER JOIN
                                              dbo.Tareas_cabecera INNER JOIN
                                              dbo.Tareas_detalle ON dbo.Tareas_cabecera.nro_orden_fabricacion = dbo.Tareas_detalle.nro_orden_fabricacion AND 
                                              dbo.Tareas_cabecera.nro_tarea = dbo.Tareas_detalle.nro_tarea ON 
                                              dbo.Orden_fabricacion.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion INNER JOIN
                                              dbo.articulos ON dbo.Orden_fabricacion.cod_articulo = dbo.articulos.cod_articulo
                       WHERE      (dbo.Tareas_cabecera.anulado = 'n') AND (dbo.Tareas_detalle.cumplido_paso = 'N') AND (dbo.Tareas_cabecera.situacion = 'i' OR
                                              dbo.Tareas_cabecera.situacion = 'p') AND (dbo.Orden_fabricacion.anulado = 'n') AND (dbo.Tareas_detalle.cod_seccion = 60)
                       ORDER BY dbo.Orden_fabricacion.nro_plan, dbo.Tareas_detalle.nro_tarea, dbo.Orden_fabricacion.nro_orden_fabricacion) tiE

GO
