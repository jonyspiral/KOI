CREATE VIEW [dbo].[documento_proveedor_aplicacion_v] AS

CREATE VIEW [dbo].[documento_proveedor_aplicacion_v] AS
	SELECT cod_documento_proveedor AS id, empresa, punto_venta, tipo_docum, nro_documento, letra,
			factura_gastos, cod_proveedor, fecha, importe_total, importe_pendiente
	FROM dbo.documento_proveedor_c
	WHERE anulado = 'N'
UNION ALL
	SELECT cod_rendicion_gastos AS id, empresa, NULL AS punto_venta, 'REN' AS tipo_docum, cod_rendicion_gastos AS nro_documento, NULL AS letra,
			'S' factura_gastos, NULL cod_proveedor, fecha_documento AS fecha, importe_total, importe_pendiente
	FROM dbo.rendicion_de_gastos
	WHERE anulado = 'N'
UNION ALL
	SELECT nro_orden_de_pago AS id, empresa, NULL AS punto_venta, 'OP' AS tipo_docum, nro_orden_de_pago AS nro_documento, NULL AS letra,
			'N' factura_gastos, cod_proveedor, fecha_documento AS fecha, importe_total, importe_pendiente
	FROM dbo.orden_de_pago
	WHERE anulado = 'N'

GO
