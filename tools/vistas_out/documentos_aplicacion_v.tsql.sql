
	CREATE VIEW documentos_aplicacion_v AS
		SELECT empresa, punto_venta, tipo_docum, nro_documento, letra, nro_comprobante, cod_cliente, fecha_documento AS fecha, importe_total, importe_pendiente
		FROM documentos_c
		WHERE anulado = 'N'
		UNION ALL
