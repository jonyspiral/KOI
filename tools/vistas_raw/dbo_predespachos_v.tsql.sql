CREATE VIEW [dbo].[predespachos_v] AS

--Es para tener cliente y sucursal en los getListObject
CREATE VIEW predespachos_v AS 
SELECT
	pre.empresa empresa,
	pre.nro_pedido nro_pedido,
	pre.nro_item nro_item,
	pe.anulado anulado,
	pec.cod_ecommerce_order cod_ecommerce_order,
	pec.cod_cliente cod_cliente,
	c.razon_social razon_social,
	pec.cod_sucursal cod_sucursal,
	pre.cod_almacen cod_almacen,
	pre.cod_articulo cod_articulo,
	pre.cod_color_articulo cod_color_articulo,
	pre.predespachados predespachados,
	pre.pred_1 pred_1,
	pre.pred_2 pred_2,
	pre.pred_3 pred_3,
	pre.pred_4 pred_4,
	pre.pred_5 pred_5,
	pre.pred_6 pred_6,
	pre.pred_7 pred_7,
	pre.pred_8 pred_8,
	pre.pred_9 pred_9,
	pre.pred_10 pred_10,
	pre.tickeados tickeados,
	pre.tick_1 tick_1,
	pre.tick_2 tick_2,
	pre.tick_3 tick_3,
	pre.tick_4 tick_4,
	pre.tick_5 tick_5,
	pre.tick_6 tick_6,
	pre.tick_7 tick_7,
	pre.tick_8 tick_8,
	pre.tick_9 tick_9,
	pre.tick_10 tick_10,
	pre.fecha_alta fecha_alta,
	pre.fecha_ultima_mod fecha_ultima_mod
FROM
	predespachos pre
INNER JOIN pedidos_d pe ON pre.empresa = pe.empresa AND pre.nro_pedido = pe.nro_pedido AND pre.nro_item = pe.nro_item
INNER JOIN pedidos_c pec ON pre.empresa = pec.empresa AND pre.nro_pedido = pec.nro_pedido
INNER JOIN clientes c ON pec.cod_cliente = c.cod_cli
GO
