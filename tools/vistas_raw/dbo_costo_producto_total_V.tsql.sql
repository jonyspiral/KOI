CREATE VIEW [dbo].[costo_producto_total_V] AS
CREATE VIEW dbo.costo_producto_total_V
AS
SELECT     costo_agrupado.cod_linea AS cod_linea, costo_agrupado.cod_articulo AS cod_articulo, costo_agrupado.denom_articulo AS denom_articulo, 
                      costo_agrupado.cod_color_articulo AS cod_color_articulo, costo_agrupado.Costo AS costo, dbo.costos_fijos_periodo_vig_v.costo_linea AS costo_linea,
                       costo_agrupado.Costo + dbo.costos_fijos_periodo_vig_v.costo_linea AS costo_total
FROM         (SELECT     cod_articulo, denom_articulo, cod_color_articulo, SUM(Costo) AS Costo, cod_linea
                       FROM          dbo.costo_mp_producto_detalle_v
                       GROUP BY cod_articulo, denom_articulo, cod_color_articulo, cod_linea) costo_agrupado INNER JOIN
                      dbo.costos_fijos_periodo_vig_v ON costo_agrupado.cod_linea = dbo.costos_fijos_periodo_vig_v.cod_linea

GO
