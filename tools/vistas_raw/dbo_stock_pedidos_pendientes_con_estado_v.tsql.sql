CREATE VIEW [dbo].[stock_pedidos_pendientes_con_estado_v] AS
CREATE VIEW dbo.stock_pedidos_pendientes_con_estado_v
AS
SELECT     TOP 100 PERCENT dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, SUM(dbo.pedidos_d.pendiente) AS cant_pend, 
                      SUM(dbo.pedidos_d.pend_1) AS pend_1, SUM(dbo.pedidos_d.pend_2) AS pend_2, SUM(dbo.pedidos_d.pend_3) AS pend_3, 
                      SUM(dbo.pedidos_d.pend_4) AS pend_4, SUM(dbo.pedidos_d.pend_5) AS pend_5, SUM(dbo.pedidos_d.pend_6) AS pend_6, 
                      SUM(dbo.pedidos_d.pend_7) AS pend_7, SUM(dbo.pedidos_d.pend_8) AS pend_8, SUM(dbo.pedidos_d.pend_9) AS pend_9, 
                      SUM(dbo.pedidos_d.pend_10) AS pend_10, dbo.pedidos_d.anulado AS anulado_d
FROM         dbo.pedidos_c INNER JOIN
                      dbo.Clientes ON dbo.pedidos_c.cod_cliente = dbo.Clientes.cod_cli INNER JOIN
                      dbo.pedidos_d ON dbo.pedidos_c.empresa = dbo.pedidos_d.empresa AND dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido
WHERE     (dbo.pedidos_c.fecha_alta > CONVERT(DATETIME, '2017-01-01 00:00:00', 102)) AND (dbo.pedidos_c.id_estado_pedido = 1 OR
                      dbo.pedidos_c.id_estado_pedido = 2 OR
                      dbo.pedidos_c.id_estado_pedido IS NULL)
GROUP BY dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, dbo.pedidos_d.anulado, dbo.pedidos_c.cod_almacen, dbo.pedidos_c.anulado
HAVING      (dbo.pedidos_d.anulado = 'N') AND (dbo.pedidos_c.cod_almacen = '01') AND (dbo.pedidos_c.anulado = 'N')
ORDER BY dbo.pedidos_c.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo

GO
