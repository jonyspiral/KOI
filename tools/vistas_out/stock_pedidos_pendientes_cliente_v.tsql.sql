CREATE VIEW dbo.stock_pedidos_pendientes_cliente_v
AS
SELECT     TOP 100 PERCENT dbo.pedidos_c.cod_cliente, dbo.pedidos_d.cod_almacen, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, 
                      SUM(ISNULL(dbo.pedidos_d.pend_1,