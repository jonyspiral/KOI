CREATE VIEW [dbo].[despachos_d_v] AS
CREATE VIEW despachos_d_v AS 
SELECT
	d.nro_despacho nro_despacho,
	d.nro_item nro_item,
	d.empresa empresa,
	d.anulado anulado,
	cli.razon_social razon_social,
	c.cod_cliente cod_cliente,
	c.cod_sucursal cod_sucursal,
	c.cod_ecommerce_order cod_ecommerce_order, 
	d.nro_pedido nro_pedido,
	d.nro_item_pedido nro_item_pedido,
	d.cod_almacen cod_almacen,
	d.cod_articulo cod_articulo,
	d.cod_color_articulo cod_color_articulo,
	d.nro_remito nro_remito,
	d.letra_remito letra_remito,
	r.nro_factura nro_factura,
	r.punto_venta_factura punto_venta_factura,
	r.tipo_docum_factura tipo_docum_factura,
	r.letra_factura letra_factura,
	d.precio_al_facturar precio_al_facturar,
	d.descuento_pedido descuento_pedido,
	d.recargo_pedido recargo_pedido,
	d.iva_porc iva_porc,
	d.precio_unitario precio_unitario,
	d.precio_unitario_final precio_unitario_final,
	d.precio_unitario_facturar precio_unitario_facturar,
	d.precio_unitario_facturar_final precio_unitario_facturar_final,
	d.cantidad cantidad,
	d.cant_1 cant_1,
	d.cant_2 cant_2,
	d.cant_3 cant_3,
	d.cant_4 cant_4,
	d.cant_5 cant_5,
	d.cant_6 cant_6,
	d.cant_7 cant_7,
	d.cant_8 cant_8,
	d.cant_9 cant_9,
	d.cant_10 cant_10,
	d.fecha_alta fecha_alta,
	d.fecha_baja fecha_baja,
	d.fecha_ultima_mod fecha_ultima_mod,
	d.cod_usuario_baja cod_usuario_baja
FROM
	despachos_d d
LEFT JOIN remitos_c r ON d.empresa = r.empresa AND d.nro_remito = r.nro_remito AND d.letra_remito = r.letra
INNER JOIN despachos_c c ON d.empresa = c.empresa AND d.nro_despacho = c.nro_despacho
INNER JOIN clientes cli ON cli.cod_cli = c.cod_cliente
GO
