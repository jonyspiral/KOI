CREATE VIEW [dbo].[stock_pedidos_predespachados] AS


/*Stock pedidos asignados sin detalle

*/
CREATE   VIEW [dbo].[stock_pedidos_predespachados]
 AS
SELECT     TOP 100 PERCENT pre.cod_almacen, pre.cod_articulo, pre.cod_color_articulo, SUM(ISNULL(pre.pred_1, 0)) AS a1, SUM(ISNULL(pre.pred_2, 0)) AS a2, 
                      SUM(ISNULL(pre.pred_3, 0)) AS a3, SUM(ISNULL(pre.pred_4, 0)) AS a4, SUM(ISNULL(pre.pred_5, 0)) AS a5, SUM(ISNULL(pre.pred_6, 0)) AS a6, 
                      SUM(ISNULL(pre.pred_7, 0)) AS a7, SUM(ISNULL(pre.pred_8, 0)) AS a8, SUM(ISNULL(pre.pred_9, 0)) AS a9, SUM(ISNULL(pre.pred_10, 0)) AS a10, 
                      SUM(ISNULL(pre.predespachados, 0)) AS cant_a
FROM         dbo.predespachos_v pre
WHERE     (pre.anulado = 'N')
GROUP BY pre.cod_almacen, pre.cod_articulo, pre.cod_color_articulo



GO
