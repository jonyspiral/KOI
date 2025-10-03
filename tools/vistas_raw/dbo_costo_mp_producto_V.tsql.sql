CREATE VIEW [dbo].[costo_mp_producto_V] AS
CREATE VIEW dbo.costo_mp_producto_V
AS
SELECT     cod_articulo, denom_articulo, cod_color_articulo, SUM(Costo) AS Costo, cod_linea
FROM         dbo.costo_mp_producto_detalle_v
GROUP BY cod_articulo, denom_articulo, cod_color_articulo, cod_linea

GO
