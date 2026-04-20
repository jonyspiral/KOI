CREATE VIEW [dbo].[tareas_sin empaque_v] AS
CREATE VIEW dbo.[tareas_sin empaque_v]
AS
SELECT     TOP 100 PERCENT *
FROM         (SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, 
                                              dbo.Tareas_cabecera.cantidad, MAX(dbo.Tareas_detalle.cod_seccion) AS [max sesion], dbo.Orden_fabricacion.cod_articulo, 
                                              dbo.articulos.denom_articulo, dbo.Orden_fabricacion.cod_color_articulo, dbo.Orden_fabricacion.version, dbo.Tareas_cabecera.tipo_tarea, 
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
                                              dbo.Tareas_cabecera.situacion = 'p') AND (dbo.Orden_fabricacion.anulado = 'n')
                       GROUP BY dbo.Orden_fabricacion.nro_plan, dbo.Orden_fabricacion.nro_orden_fabricacion, dbo.Tareas_detalle.nro_tarea, 
                                              dbo.Tareas_cabecera.cantidad, dbo.Orden_fabricacion.cod_articulo, dbo.articulos.denom_articulo, 
                                              dbo.Orden_fabricacion.cod_color_articulo, dbo.Orden_fabricacion.version, dbo.Tareas_cabecera.tipo_tarea, 
                                              dbo.Tareas_cabecera.pos_1_cant, dbo.Tareas_cabecera.pos_2_cant, dbo.Tareas_cabecera.pos_4_cant, dbo.Tareas_cabecera.pos_3_cant, 
                                              dbo.Tareas_cabecera.pos_5_cant, dbo.Tareas_cabecera.pos_6_cant, dbo.Tareas_cabecera.pos_7_cant, dbo.Tareas_cabecera.pos_8_cant) 
                      max_seccion
WHERE     ([max sesion] <> 60)
ORDER BY nro_orden_fabricacion, nro_tarea

GO
