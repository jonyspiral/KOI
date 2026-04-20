
	CREATE VIEW documentos_aplicacion_debe_v AS
		SELECT empresa, punto_venta, tipo_docum, nro_documento, letra, nro_comprobante, cod_cliente, fecha, importe_total, importe_pendiente
		FROM documentos_aplicacion_v
		WHERE (tipo_docum = 'FAC') OR (tipo_d