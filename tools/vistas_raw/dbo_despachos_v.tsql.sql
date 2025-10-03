CREATE VIEW [dbo].[despachos_v] AS

CREATE VIEW despachos_v AS 
SELECT
	d.nro_despacho_nro nro_despacho,
	d.nro_item_despacho nro_item,
	d.cod_empresa_despacho empresa,
	d.cod_cli cod_cliente,
	d.cod_suc_cli cod_sucursal,
	d.nro_pedido_nro nro_pedido,
	d.anulado anulado,
	d.pendiente pendiente,
	d.nro_remito nro_remito,
	d.letra_remito letra_remito,
	d.fecha_ultima_modificacion fecha_alta,
	d.cod_almacen cod_almacen,
	d.cod_articulo cod_articulo,
	d.cod_color cod_color,
	(CASE d.precio_al_facturar WHEN 'S' THEN d.precio_unitario ELSE (CASE c.lista_aplicable WHEN 'D' THEN a.precio_distribuidor ELSE a.precio_lista_mayor END) END) precio_unitario,
	d.cant_1 cant_1,
	d.cant_2 cant_2,
	d.cant_3 cant_3,
	d.cant_4 cant_4,
	d.cant_5 cant_5,
	d.cant_6 cant_6,
	d.cant_7 cant_7,
	d.cant_8 cant_8,
	d.cant_9 cant_9,
	d.cant_10 cant_10
FROM
	despachos_detalle d
INNER JOIN clientes c ON c.cod_cli = d.cod_cliente
INNER JOIN articulos a ON a.cod_articulo = d.cod_articulo
GO
