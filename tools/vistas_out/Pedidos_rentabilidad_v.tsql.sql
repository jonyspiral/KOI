CREATE VIEW dbo.Pedidos_rentabilidad_v
AS
SELECT     p.nro_pedido AS nro_pedido, p.cod_articulo, dbo.costo_producto_total_V.denom_articulo, p.cod_color_articulo, p.cantidad, p.precio_unitario, 
                      dbo.costo_producto_total_V.cod_linea