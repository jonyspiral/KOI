CREATE VIEW [dbo].[stock14y20_por_talle_v] AS
CREATE VIEW dbo.stock14y20_por_talle_v
AS
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_1) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_2) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_3) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_4) AS
 cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_5) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_6) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_7) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.col
ores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_8) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_9) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) AS cant_1
FROM         dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_10) AS cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_color_articulo) stock14 RIGHT OUTER JOIN
                      dbo.colores_por_articulo ON stock14.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                      stock14.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo

GO
