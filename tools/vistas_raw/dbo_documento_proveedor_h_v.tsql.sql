CREATE VIEW [dbo].[documento_proveedor_h_v] AS


CREATE VIEW [dbo].[documento_proveedor_h_v] AS
	SELECT
		h.id, h.empresa, h.cod_madre, h.cod_cancel, h.tipo_docum_cancel, h.importe, h.cod_usuario, h.fecha_alta,
		dad.fecha AS fecha_debe, dah.fecha AS fecha_haber, dad.cod_proveedor, dad.factura_gastos
	FROM documento_proveedor_h h
	LEFT JOIN documento_proveedor dad ON
		h.empresa = dad.empresa AND
		h.cod_madre = dad.id AND
		(dad.tipo_docum = 'NDB' OR dad.tipo_docum = 'FAC')
	LEFT JOIN documento_proveedor dah ON
		h.empresa = dah.empresa AND
		h.cod_cancel = dah.id AND
		h.tipo_docum_cancel = dah.tipo_docum

GO
