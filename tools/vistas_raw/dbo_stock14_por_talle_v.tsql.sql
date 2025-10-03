CREATE VIEW [dbo].[stock14_por_talle_v] AS
CREATE VIEW dbo.stock14_por_talle_v
AS
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_2 AS Talle, 
                      ISNULL(stock14.cant_2, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_2
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_3 AS Talle, 
                      ISNULL(stock14.cant_3, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_3
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_4 AS Talle, 
                      ISNULL(stock14.cant_4, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_4
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION 
ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_5 AS Talle, 
                      ISNULL(stock14.cant_5, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_5
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_6 AS Talle, 
                      ISNULL(stock14.cant_6, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_6
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_7 AS Talle, 
                      ISNULL(stock14.cant_7, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_7
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_8 AS Talle, 
                      ISNULL(stock14.cant_8, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cantidad, cant_8
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14')) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo

GO
