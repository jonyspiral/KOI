
CREATE VIEW [dbo].[documento_proveedor] AS
	SELECT
			/* campos de DocumentoProveedor */
			id, empresa, punto_venta, tipo_docum, nro_documento, letra, cod_proveedor, operacion_tipo, fecha,
			neto_gravado, neto_no_gravado, importe_total, importe_pe