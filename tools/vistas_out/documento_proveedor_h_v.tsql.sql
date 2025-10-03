

CREATE VIEW [dbo].[documento_proveedor_h_v] AS
	SELECT
		h.id, h.empresa, h.cod_madre, h.cod_cancel, h.tipo_docum_cancel, h.importe, h.cod_usuario, h.fecha_alta,
		dad.fecha AS fecha_debe, dah.fecha AS fecha_haber, dad.cod_proveedor, dad.factura_ga