CREATE OR REPLACE VIEW stock_pedidos_sin_predespacharASSELECT TOP 100 pedidos_d.cod_almacen, pedidos_d.cod_articulo, pedidos_d.cod_color_articulo, SUM(IFNULL(pedidos_d.pend_1,;;
