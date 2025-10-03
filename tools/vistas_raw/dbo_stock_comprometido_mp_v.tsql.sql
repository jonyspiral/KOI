CREATE VIEW [dbo].[stock_comprometido_mp_v] AS
CREATE VIEW dbo.stock_comprometido_mp_v
AS
SELECT     TOP 100 PERCENT dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material, 
                      SUM(ISNULL(dbo.Patrones_mp_detalle.consumo_par, 0) * ISNULL(dbo.tareas_incumplidas_v.cantidad, 0)) AS comprometido
FROM         dbo.tareas_incumplidas_v INNER JOIN
                      dbo.Patrones_mp_detalle ON dbo.tareas_incumplidas_v.cod_seccion = dbo.Patrones_mp_detalle.cod_seccion AND 
                      dbo.tareas_incumplidas_v.cod_articulo = dbo.Patrones_mp_detalle.cod_articulo AND 
                      dbo.tareas_incumplidas_v.cod_color_articulo = dbo.Patrones_mp_detalle.cod_color_articulo AND 
                      dbo.tareas_incumplidas_v.version = dbo.Patrones_mp_detalle.version
GROUP BY dbo.Patrones_mp_detalle.cod_material, dbo.Patrones_mp_detalle.cod_color_material
ORDER BY dbo.Patrones_mp_detalle.cod_material

GO
