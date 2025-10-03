CREATE VIEW dbo.stock14y20_por_talle_v
AS
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) AS cantidad, dbo.rango_talles.posic_1 AS Talle, 
                      ISNULL(stock14.cant_1, 0) A cant_1
                            FROM          dbo.stock
                            WHERE      (cod_almacen = '14') OR
                                                   (cod_almacen = '20')
                            GROUP BY cod_articulo, cod_cores_por_articulo.cod_color_articulo ON 
                      dbo.articulos.cod_articulo = dbo.colores_por_articulo.cod_articulo
UNION ALL
SELECT     dbo.articulos.cod_articulo, dbo.colores_por_articulo.cod_color_articulo, ISNULL(stock14.cantidad, 0) 