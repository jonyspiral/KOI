
	CREATE VIEW documentos_h_v AS
		SELECT h.*, dad.fecha fecha_debe, dah.fecha fecha_haber, dad.cod_cliente
		FROM documentos_h h
		INNER JOIN documentos dad ON
			h.empresa = dad.empresa AND
			h.madre_punto_venta = dad.punto_venta AND
			h.madre_t