CREATE VIEW [dbo].[stock_pedidos_pendientes_cliente_v] AS
CREATE VIEW dbo.stock_pedidos_pendientes_cliente_v
AS
SELECT     TOP 100 PERCENT dbo.pedidos_c.cod_cliente, dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, 
                      SUM(ISNULL(dbo.pedidos_d.pend_1, CONVERT(numeric, 0))) AS pend_1, SUM(ISNULL(dbo.pedidos_d.pend_2, CONVERT(numeric, 0))) AS pend_2, 
                      SUM(ISNULL(dbo.pedidos_d.pend_3, CONVERT(numeric, 0))) AS pend_3, SUM(ISNULL(dbo.pedidos_d.pend_4, CONVERT(numeric, 0))) AS pend_4, 
                      SUM(ISNULL(dbo.pedidos_d.pend_5, CONVERT(numeric, 0))) AS pend_5, SUM(ISNULL(dbo.pedidos_d.pend_6, CONVERT(numeric, 0))) AS pend_6, 
                      SUM(ISNULL(dbo.pedidos_d.pend_7, CONVERT(numeric, 0))) AS pend_7, SUM(ISNULL(dbo.pedidos_d.pend_8, CONVERT(numeric, 0))) AS pend_8, 
                      SUM(ISNULL(dbo.pedidos_d.pend_9, CONVERT(numeric, 0))) AS pend_9, SUM(ISNULL(dbo.pedidos_d.pend_10, CONVERT(numeric, 0))) AS pend_10, 
                      SUM(ISNULL(dbo.pedidos_d.pend_1, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_2, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_3, 
                      CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_4, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_5, CONVERT(numeric, 0)) 
                      + ISNULL(dbo.pedidos_d.pend_6, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_7, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_8, 
                      CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_9, CONVERT(numeric, 0)) + ISNULL(dbo.pedidos_d.pend_10, CONVERT(numeric, 0))) 
                      AS cant_pend, dbo.pedidos_d.anulado
FROM         dbo.pedidos_c INNER JOIN
                      dbo.pedidos_d ON dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido
WHERE     (dbo.pedidos_d.pendiente > 0) AND (dbo.pedidos_c.anulado = 'N')
GROUP BY dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, dbo.pedidos_d.anulado, 
                      dbo.pedidos_c.cod_cliente
HAVING      (dbo.pedidos_d.anulado = 'n')

GO
