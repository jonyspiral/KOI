

/*Stock pedidos asignados sin detalle

*/
CREATE   VIEW [dbo].[stock_pedidos_predespachados]
 AS
SELECT     TOP 100 PERCENT pre.cod_almacen, pre.cod_articulo, pre.cod_color_articulo, SUM(ISNULL(pre.pred_1, 0)) AS a1, SUM(ISNULL(pre.pred_2, 0)) AS a