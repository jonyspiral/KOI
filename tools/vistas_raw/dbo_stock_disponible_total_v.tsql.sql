CREATE VIEW [dbo].[stock_disponible_total_v] AS
CREATE VIEW dbo.stock_disponible_total_v
AS
SELECT     TOP 100 PERCENT dbo.colores_por_articulo.cod_articulo, dbo.articulos.denom_articulo, dbo.colores_por_articulo.cod_color_articulo, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.comercializacion_libre, dbo.colores_por_articulo.categoria_usuario, 
                      dbo.rango_talles.posic_1, SUM(ISNULL(disponible_total.cantidad, 0)) AS cantidad, SUM(ISNULL(disponible_total.cant_1, 0)) AS cant_1, 
                      SUM(ISNULL(disponible_total.cant_2, 0)) AS cant_2, SUM(ISNULL(disponible_total.cant_3, 0)) AS cant_3, SUM(ISNULL(disponible_total.cant_4, 0)) 
                      AS cant_4, SUM(ISNULL(disponible_total.cant_5, 0)) AS cant_5, SUM(ISNULL(disponible_total.cant_6, 0)) AS cant_6, 
                      SUM(ISNULL(disponible_total.cant_7, 0)) AS cant_7, SUM(ISNULL(disponible_total.cant_8, 0)) AS cant_8, dbo.colores_por_articulo.fotografia1
FROM         dbo.colores_por_articulo INNER JOIN
                      dbo.rango_talles INNER JOIN
                      dbo.articulos ON dbo.rango_talles.cod_rango = dbo.articulos.cod_rango ON 
                      dbo.colores_por_articulo.cod_articulo = dbo.articulos.cod_articulo LEFT OUTER JOIN
                          (SELECT     cod_articulo, cod_color_articulo, cantidad, cant_1, cant_2, cant_3, cant_4, cant_5, cant_6, cant_7, cant_8
                            FROM          dbo.stock_produccion_incumplida_v
                            UNION ALL
                            SELECT     cod_articulo, cod_color_articulo, cant_s, S1, S2, S3, S4, S5, S6, S7, S8
                            FROM         dbo.stock_menos_pendiente_vw
                            WHERE     (cod_almacen = '01')) disponible_total ON 
                      dbo.colores_por_articulo.cod_articulo = disponible_total.cod_articulo COLLATE Modern_Spanish_CI_AS AND 
                      dbo.colores_por_articulo.cod_color_articulo = disponible_total.cod_color_articulo COLLATE Modern_Spanish_CI_AS
GROUP BY dbo.colores_por_articulo.cod_articulo, dbo.articulos.denom_articulo, dbo.colores_por_articulo.cod_color_articulo, 
                      dbo.colores_por_articulo.id_tipo_producto_stock, dbo.colores_por_articulo.comercializacion_libre, dbo.colores_por_articulo.categoria_usuario, 
                      dbo.rango_talles.posic_1, dbo.colores_por_articulo.fotografia1, dbo.colores_por_articulo.vigente, dbo.articulos.vigente
HAVING      (dbo.colores_por_articulo.id_tipo_producto_stock <> '07') AND (dbo.colores_por_articulo.vigente = 'S') AND (dbo.articulos.vigente = 'S')
ORDER BY dbo.articulos.denom_articulo, dbo.colores_por_articulo.cod_color_articulo

GO
