CREATE VIEW dbo.pedidos_clientes_sin_mora_vw
AS
SELECT     TOP 100 PERCENT dbo.pedidos_detalle.cod_articulo, dbo.pedidos_detalle.cod_color_articulo, SUM(dbo.pedidos_detalle.cantidad_pendiente) 
                      AS Pendiente, SUM(dbo.pedidos_detall