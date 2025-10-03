CREATE VIEW [dbo].[stock_menos_asignado_vw] AS
CREATE VIEW dbo.stock_menos_asignado_vw
AS
SELECT     cod_almacen, cod_articulo, cod_color_articulo, SUM(cant_s) AS cant_s, SUM(S1) AS S1, SUM(S2) AS S2, SUM(S3) AS S3, SUM(S4) AS S4, SUM(S5) 
                      AS S5, SUM(S6) AS S6, SUM(S7) AS S7, SUM(S8) AS S8, SUM(S9) AS S9, SUM(S10) AS S10
FROM         (SELECT     cod_almacen, cod_articulo, cod_color_articulo, cant_s, S1, S2, S3, S4, S5, S6, S7, S8, S9, S10
                       FROM          dbo.stock_pt
                       UNION
                       SELECT     cod_almacen, cod_articulo, cod_color_articulo, SUM(- (ISNULL(predespachados, 0))) AS cant_s, SUM(- (ISNULL(pred_1, 0))) AS S1, 
                                             SUM(- (ISNULL(pred_2, 0))) AS S2, SUM(- (ISNULL(pred_3, 0))) AS S3, SUM(- (ISNULL(pred_4, 0))) AS S4, SUM(- (ISNULL(pred_5, 0))) AS S5, 
                                             SUM(- (ISNULL(pred_6, 0))) AS S6, SUM(- (ISNULL(pred_7, 0))) AS S7, SUM(- (ISNULL(pred_8, 0))) AS S8, SUM(- (ISNULL(pred_9, 0))) AS S9, 
                                             SUM(- (ISNULL(pred_10, 0))) AS S10
                       FROM         dbo.pedidos_d_v
                       WHERE     (anulado = 'N') AND (ISNULL(predespachados, 0) > 0)
                       GROUP BY cod_almacen, cod_articulo, cod_color_articulo) stock_d
GROUP BY cod_almacen, cod_articulo, cod_color_articulo

GO
