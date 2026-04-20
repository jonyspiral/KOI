CREATE VIEW [dbo].[pendientes_aplicacion_clientes_v] AS
CREATE VIEW [dbo].[pendientes_aplicacion_clientes_v] AS
	SELECT	d.cod_cliente, c.razon_social, d.empresa, d.fecha_documento,
			dbo.sumarTiempo(d.fecha_documento, 'dia', 60) fecha_vencimiento,
			d.tipo_docum, d.letra, d.nro_documento, d.observaciones,
			d.importe_total, d.importe_pendiente
	FROM	documentos_c d
			INNER JOIN clientes c ON c.cod_cli = d.cod_cliente
	WHERE	d.tipo_docum IN ('FAC', 'NDB', 'NCR') AND d.importe_pendiente > 0
			AND d.anulado = 'N'

	UNION ALL

	SELECT	r.cod_cliente, (CASE WHEN r.cod_cliente IS NULL THEN r.recibido_de ELSE c.razon_social END), r.empresa, r.fecha_documento, NULL fecha_vencimiento,
			'REC' tipo_docum, 'R' letra, r.nro_recibo nro_documento, r.observaciones,
			r.importe_total, r.importe_pendiente
	FROM	recibo r
			LEFT JOIN clientes c ON c.cod_cli = r.cod_cliente
	WHERE	r.importe_pendiente > 0 AND r.anulado = 'N'
GO
