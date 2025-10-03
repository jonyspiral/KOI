CREATE VIEW [dbo].[stock_pt] AS
CREATE VIEW dbo.stock_pt
AS
SELECT     s.cod_almacen, s.cod_articulo, s.cod_color_articulo, ISNULL(s.cant_1, 0) AS S1, ISNULL(s.cant_2, 0) AS S2, ISNULL(s.cant_3, 0) AS S3, 
                      ISNULL(s.cant_4, 0) AS S4, ISNULL(s.cant_5, 0) AS S5, ISNULL(s.cant_6, 0) AS S6, ISNULL(s.cant_7, 0) AS S7, ISNULL(s.cant_8, 0) AS S8, 
                      ISNULL(s.cant_9, 0) AS S9, ISNULL(s.cant_10, 0) AS S10, ISNULL(s.cantidad, 0) AS cant_s, al.denom_almacen AS nombre_almacen, 
                      a.denom_articulo AS nombre_articulo, cxa.denom_color AS nombre_color, rt.cod_rango_nro AS cod_rango, rt.denom_rango, rt.posic_1, 
                      cxa.id_tipo_producto_stock, cxa.fecha_validacion_stock, a.cod_linea, a.cod_marca, cxa.vigente, a.naturaleza, cxa.catalogo
FROM         dbo.stock s INNER JOIN
                      dbo.colores_por_articulo cxa ON s.cod_articulo = cxa.cod_articulo AND s.cod_color_articulo = cxa.cod_color_articulo INNER JOIN
                      dbo.articulos a ON s.cod_articulo = a.cod_articulo INNER JOIN
                      dbo.rango_talles rt ON rt.cod_rango = a.cod_rango INNER JOIN
                      dbo.Almacenes al ON al.cod_almacen = s.cod_almacen
WHERE     (cxa.vigente = 'S')

GO
