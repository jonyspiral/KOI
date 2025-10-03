CREATE VIEW [dbo].[pedidos_d_v] AS
CREATE VIEW dbo.pedidos_d_v
AS
SELECT     a.*, b.cod_cliente AS cod_cliente, cl.cod_vendedor AS cod_vendedor, b.aprobado AS aprobado, b.fecha_alta AS fecha_pedido, 
                      p.predespachados AS predespachados, p.pred_1 AS pred_1, p.pred_2 AS pred_2, p.pred_3 AS pred_3, p.pred_4 AS pred_4, p.pred_5 AS pred_5, 
                      p.pred_6 AS pred_6, p.pred_7 AS pred_7, p.pred_8 AS pred_8, p.pred_9 AS pred_9, p.pred_10 AS pred_10, p.tickeados AS tickeados, 
                      p.tick_1 AS Expr12, p.tick_2 AS Expr13, p.tick_3 AS Expr14, p.tick_4 AS Expr15, p.tick_5 AS Expr16, p.tick_6 AS Expr17, p.tick_7 AS Expr18, 
                      p.tick_8 AS Expr19, p.tick_9 AS Expr20, p.tick_10 AS Expr21, c.id_tipo_producto_stock AS Expr22, b.id_estado_pedido AS Expr1
FROM         dbo.pedidos_d a LEFT OUTER JOIN
                      dbo.predespachos p ON a.empresa = p.empresa AND a.nro_pedido = p.nro_pedido AND a.nro_item = p.nro_item INNER JOIN
                      dbo.pedidos_c b ON a.nro_pedido = b.nro_pedido INNER JOIN
                      dbo.Clientes cl ON b.cod_cliente = cl.cod_cli INNER JOIN
                      dbo.colores_por_articulo c ON a.cod_articulo = c.cod_articulo AND a.cod_color_articulo = c.cod_color_articulo
WHERE     (b.id_estado_pedido IS NULL) OR
                      (b.id_estado_pedido = 1) OR
                      (b.id_estado_pedido = 2)

GO
