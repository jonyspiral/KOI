CREATE VIEW [dbo].[documento_proveedor] AS

CREATE VIEW [dbo].[documento_proveedor] AS
	SELECT
			/* campos de DocumentoProveedor */
			id, empresa, punto_venta, tipo_docum, nro_documento, letra, cod_proveedor, operacion_tipo, fecha,
			neto_gravado, neto_no_gravado, importe_total, importe_pendiente, condicion_plazo_pago, factura_gastos, fecha_vencimiento, fecha_periodo_fiscal,
			CAST(observaciones AS VARCHAR(8000)) observaciones, documento_en_conflicto, cod_usuario, anulado, fecha_alta, fecha_baja, fecha_ultima_mod,
			/* campos de OP */
			cod_importe_operacion, imputacion, importe_sujeto_ret, beneficiario, retiene_ganancias
	FROM
	(
		SELECT
			/* campos de DocumentoProveedor */
			cod_documento_proveedor id, empresa, punto_venta, tipo_docum, nro_documento, letra, cod_proveedor, operacion_tipo, fecha,
			neto_gravado, neto_no_gravado, importe_total, importe_pendiente, condicion_plazo_pago, factura_gastos, fecha_vencimiento, fecha_periodo_fiscal,
			CAST(observaciones AS VARCHAR(8000)) observaciones, documento_en_conflicto, cod_usuario, anulado, fecha_alta, fecha_baja, fecha_ultima_mod,
			/* campos de OP */
			NULL cod_importe_operacion, NULL imputacion, NULL importe_sujeto_ret, NULL beneficiario, NULL retiene_ganancias
		FROM documento_proveedor_c
		WHERE anulado = 'N'
		UNION ALL
		SELECT
			nro_orden_de_pago id, empresa, '1' punto_venta, 'OP' tipo_docum, nro_orden_de_pago nro_documento, 'P' letra, cod_proveedor, operacion_tipo, fecha_documento fecha,
			NULL neto_gravado, NULL neto_no_gravado, importe_total, importe_pendiente, NULL condicion_plazo_pago, 'N' AS factura_gastos, NULL fecha_vencimiento, NULL fecha_periodo_fiscal,
			CAST(observaciones AS VARCHAR(8000)) observaciones, NULL documento_en_conflicto, cod_usuario, anulado, fecha_alta, fecha_baja, fecha_ultima_mod,
			/* campos de OP */
			cod_importe_operacion, imputacion, importe_sujeto_ret, beneficiario, retiene_ganancias
		FROM orden_de_pago
		WHERE anulado = 'N' AND nro_orden_de_pago > 0
		UNION ALL
		SELECT
			cod_rendicion_gastos id, empresa, '1' punto_venta, 'REN' tipo_docum, cod_rendicion_gastos nro_documento, 'R' letra, NULL cod_proveedor, 'RE' operacion_tipo, fecha_documento fecha,
			NULL neto_gravado, NULL neto_no_gravado, importe_total, importe_pendiente, NULL condicion_plazo_pago, 'S' AS factura_gastos, NULL fecha_vencimiento, NULL fecha_periodo_fiscal,
			CAST(observaciones AS VARCHAR(8000)) observaciones, NULL documento_en_conflicto, cod_usuario, anulado, fecha_alta, fecha_baja, fecha_ultima_mod,
			/* campos de REN */
			cod_importe_operacion, NULL imputacion, NULL importe_sujeto_ret, NULL beneficiario, NULL retiene_ganancias
		FROM rendicion_de_gastos
		WHERE anulado = 'N' AND cod_rendicion_gastos > 0
	) a



GO
