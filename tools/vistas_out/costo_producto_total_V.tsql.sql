CREATE VIEW dbo.costo_producto_total_V
AS
SELECT     costo_agrupado.cod_linea AS cod_linea, costo_agrupado.cod_articulo AS cod_articulo, costo_agrupado.denom_articulo AS denom_articulo, 
                      costo_agrupado.cod_color_articulo AS cod_co