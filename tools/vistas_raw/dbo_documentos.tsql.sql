CREATE VIEW [dbo].[documentos] AS


CREATE VIEW [dbo].[documentos] AS
	SELECT
			empresa, punto_venta, tipo_docum, numero, letra, nro_comprobante, anulado, tipo_docum_2, cod_cliente, cod_sucursal, cod_usuario,
			cancel_nro_documento, causa, CAST(observaciones AS VARCHAR(8000)) observaciones, fecha, fecha_alta, fecha_baja, fecha_ultima_mod,
			importe_total, importe_pendiente,
			importe_neto, importe_no_gravado, iva_importe_1, iva_porc_1, iva_importe_2, iva_porc_2, iva_importe_3, iva_porc_3, cotizacion_dolar,
			descuento_comercial_importe, descuento_comercial_porc, descuento_despacho_importe, cod_forma_pago,
			cae, cae_vencimiento, cae_obtencion_fecha, CAST(cae_obtencion_observaciones AS VARCHAR(8000)) cae_obtencion_observaciones, cae_obtencion_usuario, mail_enviado,
			tiene_detalle, dias_promedio_pago, cod_asiento_contable, cod_ecommerce_order,
			nro_recibo, cod_importe_operacion, operacion_tipo, imputacion, recibido_de, fecha_documento, fecha_ponderada_pago
	FROM
	(
		SELECT
			empresa, punto_venta, tipo_docum, nro_documento numero, letra, nro_comprobante, anulado, tipo_docum_2, cod_cliente, cod_sucursal, cod_usuario,
			cancel_nro_documento, causa, CAST(observaciones AS VARCHAR(8000)) observaciones, (CASE WHEN cae_obtencion_fecha IS NULL THEN fecha_documento ELSE cae_obtencion_fecha END) fecha, fecha_alta, fecha_baja, fecha_ultima_mod,
			importe_total, importe_pendiente,
			importe_neto, importe_no_gravado, iva_importe_1, iva_porc_1, iva_importe_2, iva_porc_2, iva_importe_3, iva_porc_3, cotizacion_dolar,
			descuento_comercial_importe, descuento_comercial_porc, descuento_despacho_importe, cod_forma_pago,
			cae, cae_vencimiento, cae_obtencion_fecha, CAST(cae_obtencion_observaciones AS VARCHAR(8000)) cae_obtencion_observaciones, cae_obtencion_usuario, mail_enviado,
			tiene_detalle, dias_promedio_pago, cod_asiento_contable, cod_ecommerce_order,
			NULL nro_recibo, NULL cod_importe_operacion, NULL operacion_tipo, NULL imputacion, NULL recibido_de, NULL fecha_documento, fecha_documento fecha_ponderada_pago
			
		FROM documentos_c
		WHERE anulado = 'N' AND cod_cliente > 0
		UNION ALL
		SELECT 
			empresa, 1 punto_venta, 'REC' tipo_docum, nro_recibo numero, 'R' letra, nro_recibo nro_comprobante, anulado, NULL tipo_docum_2, dbo.IfNullZero(cod_cliente) cod_cliente, NULL, cod_usuario,
			NULL cancel_nro_documento, NULL causa, CAST(observaciones AS VARCHAR(8000)) observaciones, fecha_documento fecha, fecha_alta, fecha_baja, fecha_ultima_mod,
			importe_total, importe_pendiente,
			NULL importe_neto, NULL importe_no_gravado, NULL iva_importe_1, NULL iva_porc_1, NULL iva_importe_2, NULL iva_porc_2, NULL iva_importe_3, NULL iva_porc_3, NULL cotizacion_dolar,
			NULL descuento_comercial_importe, NULL descuento_comercial_porc, NULL descuento_despacho_importe, NULL cod_forma_pago,
			NULL cae, NULL cae_vencimiento, NULL cae_obtencion_fecha, NULL cae_obtencion_observaciones, NULL cae_obtencion_usuario, NULL mail_enviado,
			NULL tiene_detalle, NULL dias_promedio_pago, cod_asiento_contable, cod_ecommerce_order,
			nro_recibo, cod_importe_operacion, operacion_tipo, imputacion, recibido_de, fecha_documento, fecha_ponderada_pago
		FROM recibo
		WHERE anulado = 'N' AND nro_recibo > 0
	) a
GO
