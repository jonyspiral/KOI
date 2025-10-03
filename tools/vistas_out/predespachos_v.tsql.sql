
--Es para tener cliente y sucursal en los getListObject
CREATE VIEW predespachos_v AS 
SELECT
	pre.empresa empresa,
	pre.nro_pedido nro_pedido,
	pre.nro_item nro_item,
	pe.anulado anulado,
	pec.cod_ecommerce_order cod_ecommerce_order,
	pec.cod_c