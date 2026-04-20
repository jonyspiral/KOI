CREATE VIEW dbo.stock14_por_talle_v
AS
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_5 AS Talle, 
                      ISNULL(stock14.cant_5, 0) AS cant_1
FROM         dbo.rango_talles