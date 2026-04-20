CREATE VIEW [dbo].[stock_asignados] AS
CREATE VIEW dbo.stock_asignados
AS
SELECT     TOP 100 PERCENT dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea, 
                      dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pedidos.cod_color_articulo, SUM(dbo.asignacion_pedidos.cantidad) AS cant_a, 
                      SUM(dbo.asignacion_pedidos.a_1) AS a_1, SUM(dbo.asignacion_pedidos.a_2) AS a_2, SUM(dbo.asignacion_pedidos.a_3) AS a_3, 
                      SUM(dbo.asignacion_pedidos.a_4) AS a_4, SUM(dbo.asignacion_pedidos.a_5) AS a_5, SUM(dbo.asignacion_pedidos.a_6) AS a_6, 
                      SUM(dbo.asignacion_pedidos.a_7) AS a_7, SUM(dbo.asignacion_pedidos.a_8) AS a_8, SUM(dbo.asignacion_pedidos.a_9) AS a_9, 
                      SUM(dbo.asignacion_pedidos.a_10) AS a_10, dbo.asignacion_pedidos.fecha_original_programada, dbo.asignacion_pedidos.asignado
FROM         dbo.pedidos_detalle INNER JOIN
                      dbo.pedidos_cabecera ON dbo.pedidos_detalle.cod_empresa = dbo.pedidos_cabecera.cod_empresa AND 
                      dbo.pedidos_detalle.cod_sucursal = dbo.pedidos_cabecera.cod_sucursal AND 
                      dbo.pedidos_detalle.nro_pedido = dbo.pedidos_cabecera.nro_pedido INNER JOIN
                      dbo.asignacion_pedidos ON dbo.pedidos_detalle.nro_pedido_nro = dbo.asignacion_pedidos.nro_pedido AND 
                      dbo.pedidos_detalle.nro_item = dbo.asignacion_pedidos.nro_item
WHERE     (dbo.pedidos_detalle.cantidad_pendiente > 0) AND (dbo.pedidos_detalle.anulado = 'N') AND (dbo.pedidos_cabecera.anulado = 'N')
GROUP BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea, 
                      dbo.asignacion_pedidos.cod_articulo, dbo.asignacion_pedidos.cod_color_articulo, dbo.asignacion_pedidos.fecha_original_programada, 
                      dbo.asignacion_pedidos.asignado
ORDER BY dbo.asignacion_pedidos.nro_plan, dbo.asignacion_pedidos.nro_orden_fabricacion, dbo.asignacion_pedidos.nro_tarea

GO
