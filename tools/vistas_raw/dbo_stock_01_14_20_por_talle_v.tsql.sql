CREATE VIEW [dbo].[stock_01_14_20_por_talle_v] AS
CREATE VIEW dbo.stock_01_14_20_por_talle_v
AS
SELECT     TOP 100 PERCENT cod_articulo, cod_color_articulo, Talle, cantidad, cant_1
FROM         (SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                              dbo.rango_talles.posic_1 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM          dbo.rango_talles INNER JOIN
                                              dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                  (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_1) AS cant_1
                                                    FROM          dbo.stock
                                                    WHERE      (cod_almacen = '01') OR
                                                                           (cod_almacen = '14') OR
                                                                           (cod_almacen = '20')
                                                    GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                              dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND
                                               stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                              dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_2 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_2) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_3 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_3) AS cant_1
                        
                           FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_4 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_4) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_5 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_5) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color
_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_6 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_6) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_7 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_7) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_8 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_8) AS cant_1

                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_9 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_9) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
                       UNION ALL
                       SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock_consolidado.cantidad, 0) AS cantidad, 
                                             dbo.rango_talles.posic_10 AS Talle, ISNULL(stock_consolidado.cant_1, 0) AS cant_1
                       FROM         dbo.rango_talles INNER JOIN
                                             dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango LEFT OUTER JOIN
                                                 (SELECT     cod_articulo, cod_color_articulo, SUM(cantidad) AS cantidad, SUM(cant_10) AS cant_1
                                                   FROM          dbo.stock
                                                   WHERE      (cod_almacen = '01') OR
                                                                          (cod_almacen = '14') OR
                                                                          (cod_almacen = '20')
                                                   GROUP BY cod_articulo, cod_color_articulo) stock_consolidado RIGHT OUTER JOIN
                                             dbo.colores_por_articulo ON stock_consolidado.cod_articulo COLLATE Modern_Spanish_CI_AS = dbo.colores_por_articulo.cod_articulo AND 
                                             stock_consolidado.cod_color_articulo COLLATE Modern_Spanish_CI_AS = dbo.col
ores_por_articulo.cod_color_articulo ON 
                                             dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo) DERIVEDTBL
WHERE     (NOT (Talle IS NULL))

GO
