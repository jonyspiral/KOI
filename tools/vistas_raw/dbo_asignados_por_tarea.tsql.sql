CREATE VIEW [dbo].[asignados_por_tarea] AS
CREATE VIEW dbo.asignados_por_tarea
AS
SELECT     TOP 100 PERCENT dbo.Orden_fabricacion.nro_plan AS Lote, dbo.Tareas_cabecera.nro_orden_fabricacion, dbo.Tareas_cabecera.nro_tarea, 
                      dbo.Orden_fabricacion.cod_articulo, dbo.Orden_fabricacion.cod_color_articulo, dbo.Tareas_cabecera.fecha_programacion, 
                      dbo.Tareas_cabecera.cantidad - ISNULL(a.cant_a, 0) AS Disponible, dbo.Tareas_cabecera.cantidad AS cant, ISNULL(dbo.Tareas_cabecera.pos_1_cant, 
                      0) AS c1, ISNULL(dbo.Tareas_cabecera.pos_2_cant, 0) AS c2, ISNULL(dbo.Tareas_cabecera.pos_3_cant, 0) AS c3, 
                      ISNULL(dbo.Tareas_cabecera.pos_4_cant, 0) AS c4, ISNULL(dbo.Tareas_cabecera.pos_5_cant, 0) AS c5, ISNULL(dbo.Tareas_cabecera.pos_6_cant, 0) 
                      AS c6, ISNULL(dbo.Tareas_cabecera.pos_7_cant, 0) AS c7, ISNULL(dbo.Tareas_cabecera.pos_8_cant, 0) AS c8, 
                      ISNULL(dbo.Tareas_cabecera.pos_9_cant, 0) AS c9, ISNULL(dbo.Tareas_cabecera.pos_10_cant, 0) AS c10, ISNULL(a.cant_a, 0) AS cant_a, 
                      ISNULL(a.a_1, 0) AS a1, ISNULL(a.a_2, 0) AS a2, ISNULL(a.a_3, 0) AS a3, ISNULL(a.a_4, 0) AS a4, ISNULL(a.a_5, 0) AS a5, ISNULL(a.a_6, 0) AS a6, 
                      ISNULL(a.a_7, 0) AS a7, ISNULL(a.a_8, 0) AS a8, ISNULL(a.a_9, 0) AS a9, ISNULL(a.a_10, 0) AS a10
FROM         dbo.Orden_fabricacion INNER JOIN
                      dbo.Tareas_cabecera ON dbo.Orden_fabricacion.nro_orden_fabricacion = dbo.Tareas_cabecera.nro_orden_fabricacion LEFT OUTER JOIN
                          (SELECT     TOP 100 PERCENT dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, 
                                                   dbo.asignacion_pedidos.nro_tarea, dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pedidos.cod_color_articulo, 
                                                   SUM(dbo.asignacion_pedidos.cantidad) AS cant_a, SUM(dbo.asignacion_pedidos.a_1) AS a_1, SUM(dbo.asignacion_pedidos.a_2) AS a_2,
                                                    SUM(dbo.asignacion_pedidos.a_3) AS a_3, SUM(dbo.asignacion_pedidos.a_4) AS a_4, SUM(dbo.asignacion_pedidos.a_5) AS a_5, 
                                                   SUM(dbo.asignacion_pedidos.a_6) AS a_6, SUM(dbo.asignacion_pedidos.a_7) AS a_7, SUM(dbo.asignacion_pedidos.a_8) AS a_8, 
                                                   SUM(dbo.asignacion_pedidos.a_9) AS a_9, SUM(dbo.asignacion_pedidos.a_10) AS a_10, 
                                                   dbo.asignacion_pedidos.fecha_original_programada, dbo.asignacion_pedidos.asignado
                            FROM          dbo.pedidos_detalle INNER JOIN
                                                   dbo.pedidos_cabecera ON dbo.pedidos_detalle.cod_empresa = dbo.pedidos_cabecera.cod_empresa AND 
                                                   dbo.pedidos_detalle.cod_sucursal = dbo.pedidos_cabecera.cod_sucursal AND 
                                                   dbo.pedidos_detalle.nro_pedido = dbo.pedidos_cabecera.nro_pedido INNER JOIN
                                                   dbo.asignacion_pedidos ON dbo.pedidos_detalle.nro_pedido_nro = dbo.asignacion_pedidos.nro_pedido AND 
                                                   dbo.pedidos_detalle.nro_item = dbo.asignacion_pedidos.nro_item
                            WHERE      (dbo.pedidos_detalle.cantidad_pendiente > 0) AND (dbo.pedidos_detalle.anulado = 'N') AND (dbo.pedidos_cabecera.anulado = 'N')
                            GROUP BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea, 
                                                   dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pedidos.cod_color_articulo, dbo.asignacion_pedidos.fecha_original_programada, 
                                                   dbo.asignacion_pedidos.asignado
              
              ORDER BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea) a ON 
                      dbo.Tareas_cabecera.nro_orden_fabricacion = a.nro_orden_fabricacion AND dbo.Tareas_cabecera.nro_tarea = a.nro_tarea
WHERE     (dbo.Tareas_cabecera.situacion = 'P' OR
                      dbo.Tareas_cabecera.situacion = 'I') AND (dbo.Tareas_cabecera.anulado = 'N') AND (dbo.Orden_fabricacion.anulado = 'N') AND 
                      (dbo.Orden_fabricacion.nro_plan > 0) AND (dbo.Tareas_cabecera.tipo_tarea IS NULL) AND (dbo.Tareas_cabecera.cantidad > 0)
ORDER BY dbo.Orden_fabricacion.nro_plan, dbo.Tareas_cabecera.nro_orden_fabricacion, dbo.Tareas_cabecera.fecha_programacion

GO
