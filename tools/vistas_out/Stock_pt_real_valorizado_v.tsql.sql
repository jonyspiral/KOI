CREATE VIEW dbo.Stock_pt_real_valorizado_v
AS
SELECT     TOP 100 PERCENT dbo.stock_pt.cod_almacen, dbo.stock_pt.nombre_almacen, dbo.stock_pt.cod_articulo, dbo.stock_pt.nombre_articulo, 
                      dbo.stock_pt.cod_color_articulo, dbo.stock_p