CREATE VIEW [dbo].[documentos_h_v] AS

	CREATE VIEW documentos_h_v AS
		SELECT h.*, dad.fecha fecha_debe, dah.fecha fecha_haber, dad.cod_cliente
		FROM documentos_h h
		INNER JOIN documentos dad ON
			h.empresa = dad.empresa AND
			h.madre_punto_venta = dad.punto_venta AND
			h.madre_tipo_docum = dad.tipo_docum AND
			h.madre_nro_documento = dad.numero AND
			h.madre_letra = dad.letra
		INNER JOIN documentos dah ON
			h.empresa = dah.empresa AND
			h.cancel_punto_venta = dah.punto_venta AND
			h.cancel_tipo_docum = dah.tipo_docum AND
			h.cancel_nro_documento = dah.numero AND
			h.cancel_letra = dah.letra
GO
