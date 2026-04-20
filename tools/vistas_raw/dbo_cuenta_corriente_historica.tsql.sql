CREATE VIEW [dbo].[cuenta_corriente_historica] AS

CREATE VIEW [dbo].[cuenta_corriente_historica] AS
	SELECT TOP 100 PERCENT r.empresa empresa, 1 punto_venta, 'REC' tipo_docum, '' tipo_docum_2, r.nro_recibo numero, 'R' letra, NULL nro_comprobante,
						   r.cod_cliente cod_cliente, r.fecha_documento fecha, cast(r.observaciones AS VARCHAR(300)) observaciones, -r.importe_total importe_total,
						   NULL dias_promedio_pago, NULL cae_vencimiento, 0 importe_neto
	FROM recibo r
	WHERE r.anulado = 'N'

	UNION ALL

	SELECT TOP 100 PERCENT
		empresa empresa, punto_venta punto_venta, tipo_docum tipo_docum, tipo_docum_2 tipo_docum_2, numero numero, letra letra,
		nro_comprobante nro_comprobante, cod_cliente cod_cliente, fecha fecha, observaciones observaciones,
		((CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE (1) END) * importe_total) importe_total, dias_promedio_pago, cae_vencimiento,
		((CASE tipo_docum WHEN 'NCR' THEN (-1) ELSE (1) END) * importe_neto)
	FROM documentos
	WHERE (anulado = 'N' OR anulado IS NULL) AND tipo_docum != 'REC'
GO
