CREATE VIEW [dbo].[Pedidos_rentabilidad_v] AS
CREATE VIEW dbo.Pedidos_rentabilidad_v
AS
SELECT     p.nro_pedido AS nro_pedido, p.cod_articulo, dbo.costo_producto_total_V.denom_articulo, p.cod_color_articulo, p.cantidad, p.precio_unitario, 
                      dbo.costo_producto_total_V.cod_linea, dbo.costo_producto_total_V.costo, dbo.costo_producto_total_V.costo_linea, 
                      dbo.costo_producto_total_V.costo_total, p.subtotal, dbo.costo_producto_total_V.costo_total * p.cantidad AS subt_costo, 
                      p.subtotal - dbo.costo_producto_total_V.costo_total * p.cantidad AS subt_renta
FROM         (SELECT     TOP 100 PERCENT dbo.pedidos_c.nro_pedido, dbo.pedidos_d.cod_articulo, dbo.pedidos_d.cod_color_articulo, 
                                              dbo.pedidos_d.cantidad AS cantidad, dbo.pedidos_d.precio_unitario, dbo.pedidos_d.anulado, 
                                              dbo.pedidos_d.cantidad * dbo.pedidos_d.precio_unitario AS subtotal
                       FROM          dbo.pedidos_c INNER JOIN
                                              dbo.pedidos_d ON dbo.pedidos_c.nro_pedido = dbo.pedidos_d.nro_pedido
                       WHERE      (dbo.pedidos_c.anulado = 'N') AND (dbo.pedidos_d.anulado = 'n') AND (dbo.pedidos_c.fecha_alta > CONVERT(DATETIME, 
                                              '2015-01-01 00:00:00', 102))) p INNER JOIN
                      dbo.costo_producto_total_V ON p.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.costo_producto_total_V.cod_color_articulo AND 
                      p.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.costo_producto_total_V.cod_articulo

GO
