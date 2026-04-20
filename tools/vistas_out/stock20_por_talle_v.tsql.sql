CREATE VIEW dbo.stock20_por_talle
AS
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock20.cant_1, 0) AS canL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock20.cantidad, 0) AS cantidad, dbo.rango_talles.posic_5 AS Talle, 
                      ISNULL(stock20.cant_5, 0) AS cant_1
FROM         dbo.rango_talles I