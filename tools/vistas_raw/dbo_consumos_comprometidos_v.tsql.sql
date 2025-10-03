CREATE VIEW [dbo].[consumos_comprometidos_v] AS
CREATE VIEW dbo.consumos_comprometidos_v
AS
SELECT     dbo.tareas_incumplidas_v.nro_plan, dbo.tareas_incumplidas_v.nro_orden_fabricacion, dbo.tareas_incumplidas_v.nro_tarea, 
                      dbo.tareas_incumplidas_v.cantidad, dbo.patrones_v.cod_articulo, dbo.patrones_v.denom_articulo, dbo.patrones_v.cod_color_articulo, 
                      dbo.patrones_v.version, dbo.patrones_v.conjunto, dbo.patrones_v.cod_material, dbo.patrones_v.denom_material, dbo.patrones_v.cod_color_material, 
                      dbo.patrones_v.consumo_par, dbo.patrones_v.cod_seccion, ISNULL(dbo.tareas_incumplidas_v.cantidad, 0) 
                      * dbo.patrones_v.consumo_par AS consumo_tarea, ISNULL(dbo.tareas_incumplidas_v.pos_1_cant, 0) * dbo.patrones_v.consumo_par AS cons_1, 
                      ISNULL(dbo.tareas_incumplidas_v.pos_2_cant, 0) * dbo.patrones_v.consumo_par AS cons_2, ISNULL(dbo.tareas_incumplidas_v.pos_3_cant, 0) 
                      * dbo.patrones_v.consumo_par AS cons_3, ISNULL(dbo.tareas_incumplidas_v.pos_4_cant, 0) * dbo.patrones_v.consumo_par AS cons_4, 
                      ISNULL(dbo.tareas_incumplidas_v.pos_5_cant, 0) * dbo.patrones_v.consumo_par AS cons_5, ISNULL(dbo.tareas_incumplidas_v.pos_6_cant, 0) 
                      * dbo.patrones_v.consumo_par AS cons_6, ISNULL(dbo.tareas_incumplidas_v.pos_7_cant, 0) * dbo.patrones_v.consumo_par AS cons_7, 
                      ISNULL(dbo.tareas_incumplidas_v.pos_8_cant, 0) * dbo.patrones_v.consumo_par AS cons_8, ISNULL(dbo.stock_mp.cant, 0) AS stock, 
                      ISNULL(dbo.stock_mp.c1, 0) AS stock_1, ISNULL(dbo.stock_mp.c2, 0) AS stock_2, ISNULL(dbo.stock_mp.c3, 0) AS stock_3, ISNULL(dbo.stock_mp.c4, 0) 
                      AS stock_4, ISNULL(dbo.stock_mp.c5, 0) AS stock_5, ISNULL(dbo.stock_mp.c6, 0) AS stock_6, ISNULL(dbo.stock_mp.c7, 0) AS stock_7, 
                      ISNULL(dbo.stock_mp.c8, 0) AS stock_8, dbo.stock_mp.cod_almacen
FROM         dbo.tareas_incumplidas_v INNER JOIN
                      dbo.patrones_v ON dbo.tareas_incumplidas_v.cod_articulo = dbo.patrones_v.cod_articulo AND 
                      dbo.tareas_incumplidas_v.cod_color_articulo = dbo.patrones_v.cod_color_articulo AND 
                      dbo.tareas_incumplidas_v.version = dbo.patrones_v.version AND 
                      dbo.tareas_incumplidas_v.cod_seccion = dbo.patrones_v.cod_seccion LEFT OUTER JOIN
                      dbo.stock_mp ON dbo.patrones_v.cod_material = dbo.stock_mp.cod_material AND dbo.patrones_v.cod_color_material = dbo.stock_mp.cod_color

GO
