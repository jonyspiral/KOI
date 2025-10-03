CREATE VIEW dbo.stock_01_14_20_por_talle_v
AS
SELECT     TOP 100 PERCENT cod_articulo, cod_color_articulo, Talle, cantidad, cant_1
FROM         (SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidad                           FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                 _articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_co
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '1ores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo) DERIVEDTBL
WHERE     (NOT (Talle IS NULL))
