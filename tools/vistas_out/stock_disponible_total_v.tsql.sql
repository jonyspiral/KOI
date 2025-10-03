CREATE VIEW dbo.stock_disponible_total_v
AS
SELECT     TOP 100 PERCENT dbo.colores_por_articulo.cod_articulo, dbo.articulos.denom_articulo, dbo.colores_por_articulo.cod_color_articulo, 
                      dbo.colores_por_articulo.id_tipo_producto_st