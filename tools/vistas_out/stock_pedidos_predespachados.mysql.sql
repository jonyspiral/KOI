CREATE OR REPLACE VIEW tmp_auto AS 
CREATE OR REPLACE VIEW stock_pedidos_predespachados AS SELECT pre.cod_almacen, pre.cod_articulo, pre.cod_color_articulo, SUM(IFNULL(pre.pred_1, 0)) AS a1, SUM(IFNULL(pre.pred_2, 0)) AS a LIMIT 100;
