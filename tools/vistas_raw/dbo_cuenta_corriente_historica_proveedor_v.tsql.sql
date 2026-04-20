CREATE VIEW [dbo].[cuenta_corriente_historica_proveedor_v] AS
CREATE VIEW [dbo].[cuenta_corriente_historica_proveedor_v] AS
	SELECT
		dp.empresa, dp.punto_venta, dp.tipo_docum, dp.nro_documento, dp.letra, dp.cod_proveedor, dbo.relativeDate(dp.fecha,'today',0) fecha, dp.observaciones, (case when dp.tipo_docum = 'NCR' then -dp.importe_total else dp.importe_total end) as importe_total
	FROM documento_proveedor_c dp
	WHERE dp.anulado = 'N' AND dp.factura_gastos = 'N'
	UNION ALL
	SELECT
		op.empresa, NULL punto_venta, 'OP' tipo_docum, op.nro_orden_de_pago nro_documento, '-' letra, op.cod_proveedor, dbo.relativeDate(op.fecha_documento,'today',0) fecha, op.observaciones, -op.importe_total
	FROM orden_de_pago op
	WHERE op.anulado = 'N'



GO
