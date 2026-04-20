CREATE VIEW [dbo].[stock_menos_pendiente_vw] AS
CREATE VIEW dbo.stock_menos_pendiente_vw
AS
SELECT     stock_d.cod_almacen, stock_d.cod_articulo, stock_d.cod_color_articulo, SUM(stock_d.cant_s) AS cant_s, SUM(stock_d.S1) AS S1, SUM(stock_d.S2) 
                      AS S2, SUM(stock_d.S3) AS S3, SUM(stock_d.S4) AS S4, SUM(stock_d.S5) AS S5, SUM(stock_d.S6) AS S6, SUM(stock_d.S7) AS S7, SUM(stock_d.S8) 
                      AS S8, SUM(stock_d.S9) AS S9, SUM(stock_d.S10) AS S10
FROM         (SELECT     '01' AS cod_almacen, cod_articulo, cod_color_articulo, SUM(cant_s) AS cant_s, SUM(S1) AS S1, SUM(S2) AS S2, SUM(S3) AS S3, SUM(S4) 
                                              AS S4, SUM(S5) AS S5, SUM(S6) AS S6, SUM(S7) AS S7, SUM(S8) AS S8, SUM(S9) AS S9, SUM(S10) AS S10
                       FROM          dbo.stock_pt
                       WHERE      (cod_almacen = '01' OR
                                              cod_almacen = '14' OR
                                              cod_almacen = '20')
                       GROUP BY cod_articulo, cod_color_articulo
                       UNION
                       SELECT     cod_almacen, cod_articulo, cod_color_articulo, SUM(- (ISNULL(pendiente, 0) + ISNULL(predespachados, 0))) AS cant_s, 
                                             SUM(- (ISNULL(pend_1, 0) + ISNULL(pred_1, 0))) AS S1, SUM(- (ISNULL(pend_2, 0) + ISNULL(pred_2, 0))) AS S2, SUM(- (ISNULL(pend_3, 0) 
                                             + ISNULL(pred_3, 0))) AS S3, SUM(- (ISNULL(pend_4, 0) + ISNULL(pred_4, 0))) AS S4, SUM(- (ISNULL(pend_5, 0) + ISNULL(pred_5, 0))) AS S5, 
                                             SUM(- (ISNULL(pend_6, 0) + ISNULL(pred_6, 0))) AS S6, SUM(- (ISNULL(pend_7, 0) + ISNULL(pred_7, 0))) AS S7, SUM(- (ISNULL(pend_8, 0) 
                                             + ISNULL(pred_8, 0))) AS S8, SUM(- (ISNULL(pend_9, 0) + ISNULL(pred_9, 0))) AS S9, SUM(- (ISNULL(pend_10, 0) + ISNULL(pred_10, 0))) 
                                             AS S10
                       FROM         dbo.pedidos_d_v
                       WHERE     (anulado = 'N') AND (ISNULL(pendiente, 0) + ISNULL(predespachados, 0) > 0)
                       GROUP BY cod_almacen, cod_articulo, cod_color_articulo) stock_d LEFT OUTER JOIN
                      dbo.colores_por_articulo c ON c.cod_articulo = stock_d.cod_articulo AND c.cod_color_articulo = stock_d.cod_color_articulo
WHERE     (c.comercializacion_libre <> 'A')
GROUP BY stock_d.cod_almacen, stock_d.cod_articulo, stock_d.cod_color_articulo

GO
