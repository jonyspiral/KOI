CREATE VIEW dbo.pedidos_clientes_articulo_vendedor
AS
SELECT     TOP 100 PERCENT dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, dbo.pedidos_d.pendiente, dbo.pedidos_d.cantidad AS Pedido, 
                      dbo.pedidos_d.pend_1 AS P1,