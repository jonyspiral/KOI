CREATE VIEW [dbo].[Stock_pt_real_valorizado_v] AS
CREATE VIEW dbo.Stock_pt_real_valorizado_v
AS
SELECT     TOP 100 PERCENT dbo.stock_pt.cod_almacen, dbo.stock_pt.nombre_almacen, dbo.stock_pt.cod_articulo, dbo.stock_pt.nombre_articulo, 
                      dbo.stock_pt.cod_color_articulo, dbo.stock_pt.cant_s, dbo.stock_pt.cod_linea, dbo.stock_pt.cod_marca, dbo.stock_pt.id_tipo_producto_stock, 
                      dbo.costo_producto_total_V.costo, dbo.costo_producto_total_V.costo_linea, dbo.costo_producto_total_V.costo_total
FROM         dbo.costo_producto_total_V RIGHT OUTER JOIN
                      dbo.stock_pt ON dbo.costo_producto_total_V.cod_articulo = dbo.stock_pt.cod_articulo AND 
                      dbo.costo_producto_total_V.cod_color_articulo = dbo.stock_pt.cod_color_articulo
ORDER BY dbo.stock_pt.cod_articulo

GO
